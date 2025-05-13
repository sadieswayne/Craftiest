<?php

namespace App\Http\Controllers;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Extensions\AzureTTS\System\Services\AzureService;
use App\Extensions\SpeechifyTTS\System\Services\SpeechifyService;
use App\Helpers\Classes\ApiHelper;
use App\Helpers\Classes\Helper;
use App\Models\OpenAIGenerator;
use App\Models\RateLimit;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\User;
use App\Models\UserOpenai;
use Carbon\Carbon;
use Exception;
use Google\ApiCore\ApiException;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Psr\Http\Message\StreamInterface;
use Throwable;

// comes from extension

class TTSController extends Controller
{
    private Setting $settings;

    private SettingTwo $settingsTwo;

    private OpenAIGenerator $ai;

    /**
     * TTSController constructor.
     *
     * Initializes the settings by retrieving the first instance from the database.
     */
    public function __construct()
    {
        $this->settings = Setting::getCache();
        $this->settingsTwo = SettingTwo::getCache();
        $this->ai = OpenAIGenerator::where('slug', 'ai_voiceover')->first();
    }

    /**
     * Generate speech from text input based on the specified AI engine and settings.
     *
     * @param  Request  $request  the HTTP request containing speech parameters
     *
     * @return JsonResponse
     *
     * @throws Exception
     * @throws Throwable
     */
    public function generateSpeech(Request $request)
    {
        // if (Helper::appIsDemo()) {
        //    return $this->sendErrorResponse(__('This feature is disabled in Demo version.'));
        // }

        $speeches = json_decode($request->speeches, true, 512, JSON_THROW_ON_ERROR);
        if (empty($speeches)) {
            return $this->sendErrorResponse(__('Please provide inputs.'));
        }

        if ($this->settingsTwo->daily_voice_limit_enabled) {
            $limitResponse = $this->checkDailyVoiceLimit();
            if ($limitResponse !== null) {
                return $limitResponse;
            }
        }

        $resAudio = '';
        $langsAndVoices = [];
        $wordCount = 0;

        $user = Auth::user();
        if (! $user) {
            return $this->sendErrorResponse(__('Unauthorized Access.'));
        }

        $azureService = $this->hasAzureSpeech($speeches) ? new AzureService : null;
        $speechifyService = $this->hasSpeechifySpeech($speeches) ? new SpeechifyService : null;

        foreach ($speeches as $speech) {
            $model = $this->getAIModel($speech['platform'], $speech['pace']);
            $driver = Entity::driver($model)->inputVoiceCount(1)->calculateCredit();
            $langsAndVoices['language'][] = $speech['lang'];
            $langsAndVoices['voices'][] = $speech['voice'];

            if (! $driver->hasCreditBalanceForInput()) {
                return $this->sendErrorResponse(__('Insufficient credits to generate audio.'));
            }

            try {
                $audioContent = $this->processSpeech($speech, $azureService, $speechifyService);
            } catch (ApiException|GuzzleException $e) {
                return $this->sendErrorResponse(__('Failed to connect to the AI service') . ': ' . $e->getMessage());
            }

            $resAudio .= $audioContent;
            $wordCount += $this->countWords($speech['content']);
            $driver->decreaseCredit();
        }

        $audioName = $this->storeAudio($user->id, $resAudio);
        $this->saveSpeechRecord($user, $request, $audioName, $wordCount, $langsAndVoices);

        return $this->buildResponse($request, $audioName, $user);
    }

    /**
     * Determines the appropriate AI model based on the platform and speech pace.
     *
     * @param  string  $platform  The platform identifier (e.g., Google, OpenAI, etc.).
     * @param  string  $pace  the speech pace to determine the model
     *
     * @return EntityEnum|null the corresponding AI model or null if invalid
     *
     * @throws Exception
     */
    private function getAIModel(string $platform, string $pace): ?EntityEnum
    {
        return match ($platform) {
            EngineEnum::GOOGLE->slug()     => EntityEnum::GOOGLE,
            EngineEnum::ELEVENLABS->slug() => EntityEnum::ELEVENLABS,
            EngineEnum::AZURE->slug()      => EntityEnum::AZURE,
            EngineEnum::Speechify->slug()  => EntityEnum::Speechify,
            EngineEnum::OPEN_AI->slug()    => EntityEnum::fromSlug($pace),
            default                        => throw new Exception(__('Invalid AI Model.')),
        };
    }

    /**
     * Processes the speech based on the selected AI model and platform.
     *
     * @param  array  $speech  the speech data from the request
     * @param  AzureService|null  $azureService  optional Azure service instance
     *
     * @throws ApiException
     * @throws GuzzleException
     */
    private function processSpeech(array $speech, ?AzureService $azureService, ?SpeechifyService $speechifyService): StreamInterface|JsonResponse|string
    {
        return match ($speech['platform']) {
            EngineEnum::GOOGLE->value     => $this->processGoogleSpeech($speech),
            EngineEnum::OPEN_AI->value    => $this->processOpenAISpeech($speech),
            EngineEnum::ELEVENLABS->value => $this->processElevenLabsSpeech($speech),
            EngineEnum::AZURE->value      => $this->processAzureSpeech($speech, $azureService),
            EngineEnum::Speechify->value  => $this->processSpeechifySpeech($speech, $speechifyService),
            default                       => $this->sendErrorResponse(__('Invalid platform.')),
        };
    }

    /**
     * Processes speech using Google Text-to-Speech API.
     *
     * @throws ApiException
     */
    private function processGoogleSpeech(array $speech): string
    {
        if (! $this->checkGoogleGcsFile()) {
            throw new ApiException(__('Google TTS credentials are missing or invalid.'), 419);
        }

        try {
            $client = new TextToSpeechClient([
                'credentials' => storage_path($this->settings->gcs_file),
                'project_id'  => $this->settings->gcs_name,
            ]);
        } catch (Exception $e) {
            throw new ApiException(__('Failed to connect to Google TTS service: ') . $e->getMessage(), 419);
        }

        $ssml = $this->buildSSML($speech);
        $synthesisInputSsml = (new SynthesisInput)->setSsml($ssml);
        $voice = (new VoiceSelectionParams)->setLanguageCode($speech['lang'])->setSsmlGender(SsmlVoiceGender::FEMALE);
        $audioConfig = (new AudioConfig)->setAudioEncoding(AudioEncoding::MP3);

        return $client->synthesizeSpeech($synthesisInputSsml, $voice, $audioConfig)->getAudioContent();
    }

    /**
     * Builds the SSML content for Google Text-to-Speech API.
     *
     * @param  array  $speech  the speech data from the request
     *
     * @return string the SSML content
     */
    private function buildSSML(array $speech): string
    {
        $ssml = '<speak>';
        $ssml .= sprintf(
            '<lang xml:lang="%3$s">
                        <prosody rate="%4$s">
                            <voice name="%1$s">%2$s</voice>
                            <break time="%5$ss"/>
                        </prosody>
                    </lang>',
            $speech['voice'],
            $speech['content'],
            $speech['lang'],
            $speech['pace'],
            $speech['break'],
        );

        $ssml .= '</speak>';

        return $ssml;
    }

    /**
     * Processes speech using OpenAI's API.
     *
     * @param  array  $speech  the speech data from the request
     *
     * @return StreamInterface the audio content
     *
     * @throws GuzzleException
     */
    private function processOpenAISpeech(array $speech): StreamInterface
    {
        $apiKey = $this->getOpenAIApiKey();
        $client = new Client;

        $response = $client->request('POST', 'https://api.openai.com/v1/audio/speech', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'language' => $speech['language'],
                'model'    => $speech['pace'],
                'input'    => $speech['content'],
                'voice'    => $speech['voice'],
            ],
        ]);

        return $response->getBody();
    }

    /**
     * Processes speech using ElevenLabs API.
     *
     * @param  array  $speech  the speech data from the request
     *
     * @return StreamInterface the audio content
     *
     * @throws GuzzleException
     */
    private function processElevenLabsSpeech($speech)
    {
        $apiKey = $this->settingsTwo->elevenlabs_api_key;
        $client = new Client;

        $response = $client->request('POST', 'https://api.elevenlabs.io/v1/text-to-speech/' . $speech['voice'], [
            'headers' => [
                'Content-Type' => 'application/json',
                'xi-api-key'   => $apiKey,
            ],
            'json' => [
                'text'           => $speech['content'],
                'model_id'       => 'eleven_multilingual_v2',
                'voice_settings' => [
                    'similarity_boost'  => 0.75,
                    'stability'         => 0.95,
                    'style'             => $speech['pace'] / 100,
                    'use_speaker_boost' => true,
                ],
            ],
        ]);

        return $response->getBody();
    }

    /**
     * Processes speech using Azure's Text-to-Speech API.
     *
     * @param  array  $speech  the speech data from the request
     * @param  AzureService|null  $azureService  the Azure service instance for processing speech
     *
     * @return string the audio content
     */
    private function processAzureSpeech(array $speech, ?AzureService $azureService): string
    {
        return $azureService?->synthesizeSpeech($speech['voice'], $speech['content'], $speech['lang']);
    }

    /**
     * Processes speech using Speechify's Text-to-Speech API.
     *
     * @param  array  $speech  the speech data from the request
     * @param  SpeechifyService|null  $speechifyService  the Speechify service instance for processing speech
     *
     * @return string the audio content
     */
    private function processSpeechifySpeech(array $speech, ?SpeechifyService $speechifyService): string
    {
        return $speechifyService?->synthesizeSpeech($speech['voice'], $speech['content'], $speech['lang']);
    }

    /**
     * Checks whether the Google Cloud credentials file exists.
     *
     * @return bool returns true if credentials exist, otherwise false
     */
    private function checkGoogleGcsFile(): bool
    {
        return ! empty($this->settings->gcs_file) && ! empty($this->settings->gcs_name);
    }

    /**
     * Checks if any speech in the request is using Azure platform.
     *
     * @param  array  $speeches  array of speech data
     *
     * @return bool true if any speech uses Azure platform, otherwise false
     */
    private function hasAzureSpeech(array $speeches): bool
    {
        return collect($speeches)->contains(fn ($speech) => $speech['platform'] === EngineEnum::AZURE->value);
    }

    /**
     * Checks if any speech in the request is using Speechify platform.
     *
     * @param  array  $speeches  array of speech data
     *
     * @return bool true if any speech uses Speechify platform, otherwise false
     */
    private function hasSpeechifySpeech(array $speeches): bool
    {
        return collect($speeches)->contains(fn ($speech) => $speech['platform'] === EngineEnum::Speechify->value);
    }

    /**
     * Counts the number of words in a given text.
     *
     * @param  string  $text  the input text
     *
     * @return int the word count
     */
    private function countWords(string $text): int
    {
        return str_word_count($text);
    }

    /**
     * Stores the generated audio content in storage.
     *
     * @param  int  $userId  the ID of the user
     * @param  string  $audioContent  the generated audio content
     *
     * @return string the name of the stored audio file
     */
    private function storeAudio(int $userId, string $audioContent): string
    {
        $audioName = $userId . '-' . Str::uuid() . '.mp3';
        Storage::disk('public')->put("{$audioName}", $audioContent);

        return $audioName;
    }

    /**
     * Saves the record of the generated speech in the database.
     *
     * @param  User  $user  the authenticated user
     * @param  Request  $request  the original request data
     * @param  string  $audioName  the name of the stored audio file
     * @param  int  $wordCount  the total word count of the speech content
     * @param  array  $langsAndVoices  array of languages and voices used in the speeches
     */
    private function saveSpeechRecord(User $user, Request $request, string $audioName, int $wordCount, array $langsAndVoices): void
    {
        if (! $request->input('preview')) {
            try {
                $speaches = json_decode($request->speeches, true, 512, JSON_THROW_ON_ERROR);

                if (is_array($speaches) && isset($speaches[0]['platform']) && $speaches[0]['platform'] === 'elevenlabs') {
                    if (isset($speaches[0]['name'])) {
                        $langsAndVoices['voices'][0] = $speaches[0]['name'];
                    }
                }
            } catch (Exception $e) {
            }

            UserOpenai::create([
                'team_id'    => $user->team_id,
                'title'      => $request->workbook_title,
                'slug'       => Str::random(20) . Str::slug($user?->fullName()) . '-workbook',
                'user_id'    => $user->id,
                'openai_id'  => $this->ai->id,
                'input'      => $request->speeches,
                'response'   => json_encode($langsAndVoices),
                'output'     => $audioName,
                'hash'       => Str::random(256),
                'credits'    => $wordCount,
                'words'      => $wordCount,
            ]);
        }
    }

    /**
     * Builds the response after successful audio generation.
     *
     * @param  Request  $request  the original request data
     * @param  string  $audioName  the name of the generated audio file
     * @param  User  $user  the authenticated user
     *
     * @return JsonResponse the response containing the audio URL or redirect
     *
     * @throws Throwable
     */
    private function buildResponse(Request $request, string $audioName, User $user): JsonResponse
    {
        if ($request->input('preview')) {
            return response()->json(['audioPath' => '/uploads/' . $audioName, 'output' => '<div class="data-audio" data-audio="/uploads/' . $audioName . '"><div class="audio-preview"></div></div>']);
        }

        if ($request->input('from_api')) {
            return response()->json(['audioPath' => '/uploads/' . $audioName, 'output' => '<div class="data-audio" data-audio="/uploads/' . $audioName . '"><div class="audio-preview"></div></div>']);
        }

        $userOpenai = UserOpenai::where('user_id', $user->id)->where('openai_id', $this->ai->id)->orderBy('created_at', 'desc')->paginate(10);
        $userOpenai->withPath(route('dashboard.user.openai.generator', $this->ai->slug));
        $openai = $this->ai;
        $html2 = view('panel.user.openai.components.generator_sidebar_table', compact('userOpenai', 'openai'))->render();

        return response()->json(compact('html2'));
    }

    /**
     * Check if the user has reached the daily voice generation limit.
     */
    private function checkDailyVoiceLimit(): ?JsonResponse
    {
        $ipAddress = $this->getClientIp();
        $dbIpAddress = RateLimit::where('ip_address', $ipAddress)
            ->where('type', 'voice')
            ->first();

        if ($dbIpAddress) {
            // Reset attempts if a new day has started
            if ($this->isNewDay($dbIpAddress->last_attempt_at)) {
                $dbIpAddress->attempts = 0;
            }
        } else {
            // Create a new rate limit entry if none exists
            $dbIpAddress = new RateLimit(['ip_address' => $ipAddress]);
        }

        // Check if the user has exceeded their daily limit
        if ($dbIpAddress->attempts >= $this->settingsTwo->allowed_voice_count) {
            return $this->sendErrorResponse($this->getExceededLimitMessage());
        }

        // Increment the attempts and update the timestamp
        $this->incrementAttempts($dbIpAddress);

        return null; // No limit exceeded, continue with the process
    }

    /**
     * Increment attempts and update the last attempt time.
     */
    private function incrementAttempts(RateLimit $rateLimit): void
    {
        $rateLimit->attempts++;
        $rateLimit->type = 'voice';
        $rateLimit->last_attempt_at = now();
        $rateLimit->save();
    }

    /**
     * Check if the last attempt was made on a different day.
     */
    private function isNewDay(string $lastAttemptAt): bool
    {
        return now()->diffInDays(Carbon::parse($lastAttemptAt)) > 0;
    }

    /**
     * Get the appropriate message when the daily limit is exceeded.
     */
    private function getExceededLimitMessage(): string
    {
        return Helper::appIsDemo()
            ? __('You have reached the maximum number of voice generation allowed on the demo.')
            : __('You have reached the maximum number of voice generation allowed.');
    }

    /**
     * Send an error response with the specified message.
     */
    private function sendErrorResponse(string $message): JsonResponse
    {
        return response()->json(['errors' => [$message]], 429);
    }

    /**
     * Get the client IP address, considering potential use of Cloudflare.
     */
    private function getClientIp(): string
    {
        return request()?->header('CF-Connecting-IP') ?? request()->ip();
    }

    /**
     * Get the OpenAI API key to use for the request.
     */
    private function getOpenAIApiKey(): string
    {
        return ApiHelper::setOpenAiKey();
    }
}
