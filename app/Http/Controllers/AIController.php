<?php

namespace App\Http\Controllers;

use App\Domains\Engine\Enums\EngineEnum;
use App\Domains\Engine\Services\FalAIService;
use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Enums\BedrockEngine;
use App\Extensions\Midjourney\System\Services\PiAPIService;
use App\Helpers\Classes\ApiHelper;
use App\Helpers\Classes\Helper;
use App\Models\Company;
use App\Models\OpenAIGenerator;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\Usage;
use App\Models\User;
use App\Models\UserOpenai;
use App\Services\Bedrock\BedrockRuntimeService;
use App\Services\Youtube\YoutubeTranscriptService;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JsonException;
use OpenAI;
use OpenAI\Laravel\Facades\OpenAI as FacadesOpenAI;
use RuntimeException;
use Throwable;

class AIController extends Controller
{
    protected BedrockRuntimeService $bedrockService;

    protected $client;

    protected $settings;

    protected $settings_two;

    public const LOADING_GIF = '/themes/default/assets/img/loading.svg';

    public const STORAGE_S3 = 's3';

    public const CLOUDFLARE_R2 = 'r2';

    public function __construct(BedrockRuntimeService $bedrockService)
    {
        $this->bedrockService = $bedrockService;
        $this->middleware(function (Request $request, $next) {
            ApiHelper::setOpenAiKey();

            return $next($request);
        });
        // Settings
        $this->settings = Setting::getCache();
        $this->settings_two = SettingTwo::getCache();
        set_time_limit(120);
    }

    /**
     * @throws Throwable
     * @throws GuzzleException
     * @throws JsonException
     */
    public function buildOutput(Request $request)
    {
        $prompt = '';
        $request->merge([
            'maximum_length' => $request->has('maximum_length') ? ($request->input('maximum_length') === 'undefined' ? null : $request->input('maximum_length')) : null,
        ]);
        if (setting('hide_output_length_option') !== 1 && $request->has('maximum_length')) {
            $request->validate([
                'maximum_length' => 'sometimes|present|nullable|integer|min:1',
            ]);
        }

        if ($request->has('number_of_results')) {
            $request->validate(['number_of_results' => 'sometimes|nullable|integer|min:1']);
        }

        $user = Auth::user();
        $image_generator = $request->image_generator;
        $post_type = $request->post_type;

        // SETTINGS
        $number_of_results = $request->number_of_results ?? 1;
        $maximum_length = $request->maximum_length ?? $this->settings->openai_max_input_length;
        $creativity = $request->creativity ?? $this->settings->openai_default_creativity;

        $language = $request->language;

        try {
            $language = explode('-', $language);
            if (count($language) > 1 && config('localization.supportedLocales.' . $language[0] . '.name')) {
                $ek = $language[1];
                $language = config('localization.supportedLocales.' . $language[0] . '.name');
                $language .= " $ek";
            } else {
                $language = $request->language;
            }
        } catch (Throwable $th) {
            $language = $request->language;
            Log::error($language);
        }

        $negative_prompt = $request->negative_prompt;
        $tone_of_voice = $request->tone_of_voice;
        if ($request->tone_of_voice_custom) {
            $tone_of_voice = $request->tone_of_voice_custom;
        }
        if (! $tone_of_voice) {
            $tone_of_voice = $this->settings->openai_default_tone_of_voice;
        }

        // POST GENERATOR
        if ($post_type === 'post_generator') {
            $description = $request->description;
            $prompt = "Write a post about $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different posts. Tone of voice must be $tone_of_voice";
        }

        // POST TITLE GENERATOR
        if ($post_type === 'post_title_generator') {
            $your_description = $request->your_description;
            $prompt = "Post title about $your_description in language $language .Generate $number_of_results post titles. Tone $tone_of_voice.";
        }

        // ARTICLE GENERATOR
        if ($post_type === 'article_generator') {
            $article_title = $request->article_title;
            $focus_keywords = $request->focus_keywords;
            $prompt = "Generate article about $article_title. Focus on $focus_keywords. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice";
        }

        // SUMMARY GENERATOR SUMMARIZER SUMMARIZE TEXT
        if ($post_type === 'summarize_text') {
            $text_to_summary = $request->text_to_summary;
            $tone_of_voice = $request->tone_of_voice;
            if ($request->tone_of_voice_custom) {
                $tone_of_voice = $request->tone_of_voice_custom;
            }

            $prompt = "Summarize the following text: $text_to_summary in $language using a tone of voice that is $tone_of_voice. The summary should be no longer than $maximum_length words and set the creativity to $creativity in terms of creativity. Generate $number_of_results different summaries.";
        }

        // PRODUCT DESCRIPTION
        if ($post_type === 'product_description') {
            $product_name = $request->product_name;
            $description = $request->description;

            $prompt = "Write product description for $product_name. The language is $language. Maximum length is $maximum_length. Creativity is $creativity between 0 to 1. see the following information as a starting point: $description. Generate $number_of_results different product descriptions. Tone $tone_of_voice.";
        }

        // PRODUCT NAME
        if ($post_type === 'product_name') {
            $seed_words = $request->seed_words;
            $product_description = $request->product_description;

            $prompt = "Generate product names that will appeal to customers who are interested in $seed_words. These products should be related to $product_description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different product names. Tone of voice must be $tone_of_voice";
        }

        // TESTIMONIAL REVIEW GENERATOR
        if ($post_type === 'testimonial_review') {
            $subject = $request->subject;
            $prompt = "Generate testimonial for $subject. Include details about how it helped you and what you like best about it. Be honest and specific, and feel free to get creative with your wording Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different testimonials. Tone of voice must be $tone_of_voice";
        }

        // PROBLEM AGITATE SOLUTION
        if ($post_type === 'problem_agitate_solution') {
            $description = $request->description;

            $prompt = "Write Problem-Agitate-Solution copy for the $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. problem-agitate-solution. Tone of voice must be $tone_of_voice Generate $number_of_results different Problem-Afitate-Solution.";
        }

        // BLOG SECTION
        if ($post_type === 'blog_section') {
            $description = $request->description;

            $prompt = " Write me blog section about $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog sections. Tone of voice must be $tone_of_voice";
        }

        // BLOG POST IDEAS
        if ($post_type === 'blog_post_ideas') {
            $description = $request->description;

            $prompt = "Write blog post article ideas about $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog post ideas. Tone of voice must be $tone_of_voice";
        }

        // BLOG INTROS
        if ($post_type === 'blog_intros') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write blog post intro about title: $title. And the description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog intros. Tone of voice must be $tone_of_voice";
        }

        // BLOG CONCLUSION
        if ($post_type === 'blog_conclusion') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write blog post conclusion about title: $title. And the description is $description.Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different blog conclusions. Tone of voice must be $tone_of_voice";
        }

        // FACEBOOK ADS
        if ($post_type === 'facebook_ads') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write facebook ads text about title: $title. And the description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different facebook ads text. Tone of voice must be $tone_of_voice";
        }

        // YOUTUBE VIDEO DESCRIPTION
        if ($post_type === 'youtube_video_description') {
            $title = $request->title;

            $prompt = "write youtube video description about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video descriptions. Tone of voice must be $tone_of_voice";
        }

        // YOUTUBE VIDEO TITLE
        if ($post_type === 'youtube_video_title') {
            $description = $request->description;

            $prompt = "Craft captivating, attention-grabbing video titles about $description for YouTube rankings. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video titles. Tone of voice must be $tone_of_voice";
        }

        // YOUTUBE VIDEO TAG
        if ($post_type === 'youtube_video_tag') {
            $title = $request->title;

            $prompt = "Generate tags and keywords about $title for youtube video. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different youtube video tags. Tone of voice must be $tone_of_voice";
        }

        // INSTAGRAM CAPTIONS
        if ($post_type === 'instagram_captions') {
            $title = $request->title;

            $prompt = "Write instagram post caption about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different instagram captions. Tone of voice must be $tone_of_voice";
        }

        // INSTAGRAM HASHTAG
        if ($post_type === 'instagram_hashtag') {
            $keywords = $request->keywords;

            $prompt = "Write instagram hastags for $keywords. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different instagram hashtags. Tone of voice must be $tone_of_voice";
        }

        // SOCIAL MEDIA POST TWEET
        if ($post_type === 'social_media_post_tweet') {
            $title = $request->title;

            $prompt = "Write in 1st person tweet about $title. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different tweets. Tone of voice must be $tone_of_voice";
        }

        // SOCIAL MEDIA POST BUSINESS
        if ($post_type === 'social_media_post_business') {
            $company_name = $request->company_name;
            $provide = $request->provide;
            $description = $request->description;

            $prompt = "Write in company social media post, company name: $company_name. About: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different social media posts. Tone of voice must be $tone_of_voice";
        }

        // FACEBOOK HEADLINES
        if ($post_type === 'facebook_headlines') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Write Facebook ads title about title: $title. And description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different facebook ads title. Tone of voice must be $tone_of_voice";
        }

        // GOOGLE ADS HEADLINES
        if ($post_type === 'google_ads_headlines') {
            $product_name = $request->product_name;
            $description = $request->description;
            $audience = $request->audience;

            $prompt = "Write Google ads headline product name: $product_name. Description is $description. Audience is $audience. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different google ads headlines. Tone of voice must be $tone_of_voice";
        }

        // GOOGLE ADS DESCRIPTION
        if ($post_type === 'google_ads_description') {
            $product_name = $request->product_name;
            $description = $request->description;
            $audience = $request->audience;

            $prompt = "Write google ads description product name: $product_name. Description is $description. Audience is $audience. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different google ads description. Tone of voice must be $tone_of_voice";
        }

        // CONTENT REWRITE
        if ($post_type === 'content_rewrite') {
            $contents = $request->contents;

            $prompt = "Rewrite content:  '$contents'. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different rewrited content. Tone of voice must be $tone_of_voice";
        }

        // PARAGRAPH GENERATOR
        if ($post_type === 'paragraph_generator') {
            $description = $request->description;
            $keywords = $request->keywords;

            $prompt = "Generate one paragraph about:  '$description'. Keywords are $keywords. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different paragraphs. Tone of voice must be $tone_of_voice";
        }

        // Pros & Cons
        if ($post_type === 'pros_cons') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Generate pros & cons about title:  '$title'. Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different pros&cons. Tone of voice must be $tone_of_voice";
        }

        // META DESCRIPTION
        if ($post_type === 'meta_description') {
            $title = $request->title;
            $description = $request->description;
            $keywords = $request->keywords;

            $prompt = "Generate website meta description site name: $title. Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different meta descriptions. Tone of voice must be $tone_of_voice";
        }

        // FAQ Generator (All datas)
        if ($post_type === 'faq_generator') {
            $title = $request->title;
            $description = $request->description;

            $prompt = "Answer like faq about subject: $title Description is $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different faqs. Tone of voice must be $tone_of_voice";
        }

        // Email Generator
        if ($post_type === 'email_generator') {
            $subject = $request->subject;
            $description = $request->description;

            $prompt = "Write email about title: $subject, description: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different emails. Tone of voice must be $tone_of_voice";
        }

        // Email Answer Generator
        if ($post_type === 'email_answer_generator') {
            $description = $request->description;

            $prompt = "answer this email content: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different email answers. Tone of voice must be $tone_of_voice";
        }

        // Newsletter Generator
        if ($post_type === 'newsletter_generator') {
            $description = $request->description;
            $subject = $request->subject;
            $title = $request->title;

            $prompt = "generate newsletter template about product_title: $title, reason: $subject description: $description. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different newsletter template. Tone of voice must be $tone_of_voice";
        }

        // Grammar Correction
        if ($post_type === 'grammar_correction') {
            $description = $request->description;

            $prompt = "Correct this to standard $language. Text is '$description'. Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different grammar correction. Tone of voice must be $tone_of_voice";
        }

        // TL;DR summarization
        if ($post_type === 'tldr_summarization') {
            $description = $request->description;

            $prompt = "$description. Tl;dr Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Language is $language. Generate $number_of_results different tl;dr. Tone of voice must be $tone_of_voice";
        }

        if ($post_type === 'ai_rewriter') {
            $content_rewrite = $request->content_rewrite;
            $rewrite_mode = $request->rewrite_mode;

            $prompt = "Original Content: $content_rewrite.\n\n\nMust Rewrite content with $rewrite_mode mode differently with original content. Result language is $language \n";
        }

        if ($post_type === 'ai_image_generator') {
            $imageParam = $request->all();
        }

        if ($post_type === 'ai_video') {
            $videoParam = $request->all();
        }

        if ($post_type === 'ai_code_generator') {
            $description = $request->description;
            $code_language = $request->code_language;
            $prompt = "Write a code about $description, in $code_language";
        }

        $post = OpenAIGenerator::where('slug', $post_type)->first();

        if ((int) $post->custom_template === 1) {
            $custom_template = OpenAIGenerator::find($request->openai_id);
            $prompt = $custom_template->prompt;

            if (! empty($request->description)) {
                $prompt .= ' customer prompt: [' . $request->description . ']';
            }

            foreach (json_decode($custom_template->questions, false, 512, JSON_THROW_ON_ERROR) as $question) {
                $question_name = '**' . $question->name . '**';
                $prompt = str_replace($question_name, $request[$question->name], $prompt);
            }

            $minimum_length = (int) $maximum_length * 0.9;

            $prompt .= " in $language language. Number of results should be $number_of_results.And exactly minimum length of $minimum_length.And exactly the maximum length of $maximum_length characters.";

            if ($creativity !== 'undefined') {
                $prompt .= " Creativity is $creativity between 0 and 1.";
            }
            if ($tone_of_voice !== 'undefined') {
                $prompt .= " Tone of voice must be $tone_of_voice.";
            }
        }

        if ($post->type === 'youtube') {
            $language = $request->language;
            $youtube_action = $request->youtube_action;
            if ($youtube_action === 'blog') {
                $prompt = "You are blog writer. Turn the given transcript text into a blog post in and translate to {$language} language. Group the content and create a subheading (with HTML-h2) for each group (without HTML body or head tags or backticks ```html). Maximum $maximum_length words. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            } elseif ($youtube_action === 'short') {
                $prompt = "You are transcript editor. Make sense of the given content and explain the main idea. Your result must be in {$language} language. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            } elseif ($youtube_action === 'list') {
                $prompt = "You are transcript editor. Make sense of the given content and make a list main ideas. Your result must be in {$language} language. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            } elseif ($youtube_action === 'tldr') {
                $prompt = "You are transcript editor. Make short TLDR. Your result must be in {$language} language. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            } elseif ($youtube_action === 'prons_cons') {
                $prompt = "You are transcript editor. Make short pros and cons. Your result must be in {$language} language. Creativity is $creativity between 0 and 1. Generate $number_of_results different articles. Tone of voice must be $tone_of_voice. Content:";
            }

            $videoUrl = $request->url;

            $request->validate([
                'url' => 'required|url',
            ]);

            $data = (new youtubeTranscriptService)
                ->getTranscript($videoUrl);
            $transcripts = json_decode($data->content(), true);

            if (isset($transcripts['captions'])) {
                $prompt .= $transcripts['captions'] . "\n";
            } else {
                return response()->json([
                    'status'  => 'error',
                    'message' => __('There are no captions available in the video.'),
                ], 401);
            }
        }

        if ($post->type === 'rss') {
            $language = $request->language;
            $prompt = "write blog post about {$request->title}. Group the content and create a subheading (with HTML-h2) for each group (without HTML body or head tags or backticks ```html).";
            $prompt .= "Your result must be in $language language. Number of results should be $number_of_results. And the maximum length of $maximum_length characters. Tone of voice must be $tone_of_voice. Creativity is $creativity between 0 and 1.";
        }

        // check if there is a company input included in the request
        if ($request->company) {
            $company = Company::find($request->company);
            $product = Product::find($request->product);
            if ($company) {
                if (! isset($prompt)) {
                    $prompt = '';
                }
                $type = $product->type == 0 ? 'Service' : 'Product';
                $prompt .= ".\n Focus on my company and {$type}'s information: \n";
                // Company information
                if ($company->name) {
                    $prompt .= "The company's name is {$company->name}. ";
                }
                // explode industry
                $industry = explode(',', $company->industry);
                $count = count($industry);
                if ($count > 0) {
                    $prompt .= 'The company is in the ';
                    foreach ($industry as $index => $ind) {
                        $prompt .= $ind;
                        if ($index < $count - 1) {
                            $prompt .= ' and ';
                        }
                    }
                }

                if ($company->website) {
                    $prompt .= ". The company's website is {$company->website}. ";
                }

                if ($company->target_audience) {
                    $prompt .= "The company's target audience is: {$company->target_audience}. ";
                }

                if ($company->tagline) {
                    $prompt .= "The company's tagline is {$company->tagline}. ";
                }

                if ($company->description) {
                    $prompt .= "The company's description is {$company->description}. ";
                }
                if ($product) {
                    // Product information
                    if ($product->key_features) {
                        $prompt .= "The {$product->type}'s key features are {$product->key_features}. ";
                    }

                    if ($product->name) {
                        $prompt .= "The {$product->type}'s name is {$product->name}. \n";
                    }
                }
            }
        }

        return match ($post->type) {
            'text', 'rss', 'youtube' => $this->textOutput($prompt, $post, $creativity, $maximum_length, $number_of_results, $user),
            'code'     => $this->codeOutput($prompt, $post, $user),
            'image'    => $this->imageOutput($imageParam, $post, $user),
            'video'    => $this->videoOutput($videoParam),
            'audio'    => $this->audioOutput($request->file('file'), $post, $user),
            'isolator' => $this->audioIsolator($request->file('file'), $post, $user),
            default    => response()->json([
                'status'  => 'error',
                'message' => __('Invalid post type.'),
            ], 401),
        };
    }

    /**
     * @throws Exception
     */
    public function streamedTextOutput(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $message_id = $request->input('message_id', null);
        $prompt = $request->input('prompt', null);
        $creativity = $request->input('creativity', null);
        $maximum_length = $request->input('maximum_length', null);
        $number_of_results = $request->input('number_of_results', null);
        $youtube_url = $request->input('youtube_url', null);
        $rss_image = $request->input('rss_image', null);
        $openAiMessage = UserOpenai::whereId($message_id)->first();
        $image_storage = $this->settings_two->ai_image_storage;
        $driver = Entity::driver();
        $driver->redirectIfNoCreditBalance();

        return response()->stream(function () use ($image_storage, $driver, $openAiMessage, $prompt, $creativity, $maximum_length, $number_of_results, $youtube_url, $rss_image) {
            try {
                if ($driver->enum()->value === EntityEnum::TEXT_DAVINCI_003->value) {
                    $stream = FacadesOpenAI::completions()->createStreamed([
                        'model'       => $driver->enum()->value,
                        'prompt'      => $prompt,
                        'temperature' => (float) $creativity,
                        'max_tokens'  => (int) $maximum_length,
                        'n'           => (int) $number_of_results,
                    ]);
                } else {
                    if ((int) $number_of_results > 1) {
                        $prompt .= ' number of results should be ' . (int) $number_of_results;
                    }
                    $stream = FacadesOpenAI::chat()->createStreamed([
                        'model'    => $driver->enum()->value,
                        'messages' => [['role' => 'user', 'content' => $prompt]],
                    ]);
                }
            } catch (Exception $exception) {
                $messageError = 'Error from API call. Please try again. If error persists, contact the system administrator with this message: ' . $exception->getMessage();
                echo "data: $messageError\n\n";
                flush();
                echo "data: [DONE]\n\n";
                flush();
                usleep(50000);
            }
            $total_used_tokens = 0;
            $output = '';
            $responsedText = '';
            if ($youtube_url) {
                $parsedUrl = parse_url($youtube_url);
                if (isset($parsedUrl['query'])) {
                    parse_str($parsedUrl['query'], $queryParameters);
                    if (isset($queryParameters['v'])) {
                        $video_id = $queryParameters['v'];
                    }
                }
                $video_thumbnail = sprintf('https://img.youtube.com/vi/%s/maxresdefault.jpg', $video_id);

                $contents = file_get_contents($video_thumbnail);
                $nameOfImage = "youtube-$video_id.jpg";

                // save file on local storage or aws s3
                Storage::disk('public')->put($nameOfImage, $contents);
                $path = '/uploads/' . $nameOfImage;

                if ($image_storage === self::STORAGE_S3) {
                    try {
                        $uploadedFile = new File(substr($path, 1));

                        $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                        unlink(substr($path, 1));
                        $path = Storage::disk('s3')->url($aws_path);
                    } catch (Exception $e) {
                        return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
                    }
                }

                $output = "<img src=\"$path\" style=\"width:100%\"><br><br>";

                $total_used_tokens++;
                $needChars = 6000 - 1;
                $random_text = Str::random($needChars);
                echo 'data: ' . $output . '/**' . $random_text . "\n\n";
                flush();
                usleep(500);
            }
            if ($rss_image) {
                $contents = file_get_contents($rss_image);
                $nameOfImage = 'rss-' . Str::random(12) . '.jpg';
                Storage::disk('public')->put($nameOfImage, $contents);
                $path = '/uploads/' . $nameOfImage;
                if ($image_storage === self::STORAGE_S3) {
                    try {
                        $uploadedFile = new File(substr($path, 1));

                        $aws_path = Storage::disk('s3')->put('', $uploadedFile);
                        unlink(substr($path, 1));
                        $path = Storage::disk('s3')->url($aws_path);
                    } catch (Exception $e) {
                        return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
                    }
                }

                $output = "<img src=\"$path\" style=\"width:100%\"><br><br>";

                $total_used_tokens++;
                $needChars = 6000 - 1;
                $random_text = Str::random($needChars);
                echo 'data: ' . $output . '/**' . $random_text . "\n\n";
                // ob_flush();
                flush();
                usleep(500);
            }
            foreach ($stream as $response) {
                if ($driver->enum()->value === EntityEnum::TEXT_DAVINCI_003->value) {
                    if (isset($response->choices[0]->text)) {
                        $message = $response->choices[0]->text;
                        $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);
                        $output .= $messageFix;
                        $responsedText .= $message;
                        $total_used_tokens += countWords($messageFix);

                        $string_length = Str::length($messageFix);
                        $needChars = 6000 - $string_length;
                        $random_text = Str::random($needChars);
                        echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                        // ob_flush();
                        flush();
                        usleep(500);
                    }
                } else {
                    if (isset($response['choices'][0]['delta']['content'])) {
                        $message = $response['choices'][0]['delta']['content'];
                        $messageFix = str_replace(["\r\n", "\r", "\n"], '<br/>', $message);
                        $output .= $messageFix;
                        $responsedText .= $message;
                        $total_used_tokens += countWords($messageFix);

                        $string_length = Str::length($messageFix);
                        $needChars = 6000 - $string_length;
                        $random_text = Str::random($needChars);

                        echo 'data: ' . $messageFix . '/**' . $random_text . "\n\n";
                        flush();
                        usleep(500);
                    }
                }

                if (connection_aborted()) {
                    break;
                }
            }

            $openAiMessage->response = $responsedText;
            $openAiMessage->output = $output;
            $openAiMessage->hash = Str::random(256);
            $openAiMessage->credits = $total_used_tokens;
            $openAiMessage->words = 0;
            $openAiMessage->save();

            $driver->input($responsedText)->calculateCredit()->decreaseCredit();
            Usage::getSingle()->updateWordCounts($driver->calculate());

            echo 'data: [DONE]';
            echo "\n\n";
            flush();
            usleep(50000);
        }, 200, [
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type'      => 'text/event-stream',
        ]);
    }

    public function textOutput($prompt, $post, $creativity, $maximum_length, $number_of_results, $user): JsonResponse
    {
        $prompt = $this->applyPromptRules($prompt);
        $entry = UserOpenai::create([
            'team_id'   => $user->team_id,
            'title'     => request('title') ?: null,
            'slug'      => str()->random(7) . str($user?->fullName())->slug() . '-workbook',
            'user_id'   => $user->id,
            'openai_id' => $post->id,
            'input'     => $prompt,
            'response'  => null,
            'output'    => null,
            'hash'      => str()->random(256),
            'credits'   => 0,
            'words'     => 0,
        ]);

        $message_id = $entry->id;
        $workbook = $entry;
        $inputPrompt = $prompt;
        $html = view('panel.user.openai.documents_workbook_textarea', compact('workbook'))->render();

        return response()->json(compact('message_id', 'html', 'creativity', 'maximum_length', 'number_of_results', 'inputPrompt'));
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function codeOutput($prompt, $post, $user): JsonResponse
    {
        $driver = Entity::driver();
        $driver->redirectIfNoCreditBalance();
        if ($driver->enum()->value === EntityEnum::TEXT_DAVINCI_003->value) {
            $response = FacadesOpenAI::completions()->create([
                'model'      => $driver->enum()->value,
                'prompt'     => $prompt,
                'max_tokens' => (int) $this->settings->openai_max_output_length,
            ]);
        } else {
            $response = FacadesOpenAI::chat()->create([
                'model'    => $driver->enum()->value,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);
        }
        $entry = new UserOpenai([
            'team_id'     => $user->team_id,
            'title'       => request('title') ?: null,
            'slug'        => Str::random(7) . Str::slug($user?->fullName()) . '-workbook',
            'user_id'     => Auth::id(),
            'openai_id'   => $post->id,
            'input'       => $prompt,
            'response'    => json_encode($response->toArray(), JSON_THROW_ON_ERROR),
        ]);

        if ($driver->enum()->value === EntityEnum::TEXT_DAVINCI_003->value) {
            $entry->output = $response['choices'][0]['text'];
        } else {
            $entry->output = $response->choices[0]->message->content;
        }
        $entry->hash = Str::random(256);
        $entry->credits = countWords($entry->output);
        $entry->words = 0;
        $entry->save();

        $driver->input($entry->output)->calculateCredit()->decreaseCredit();
        Usage::getSingle()->updateWordCounts($driver->calculate());

        $workbook = $entry;
        $html = view('panel.user.openai.documents_workbook_textarea', compact('workbook'))->render();
        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
        $openai = OpenAIGenerator::find($post->id);
        $html2 = view('panel.user.openai.components.generator_sidebar_table', compact('userOpenai', 'openai'))->render();

        return response()->json(compact('html', 'html2'));
    }

    /**
     * @throws Exception
     */
    public function chatImageOutput(Request $request): JsonResponse
    {
        $apiKey = $this->getOpenAiApiKey(Auth::user());
        config(['openai.api_key' => $apiKey]);
        set_time_limit(120);
        $driver = Entity::driver();

        $chkLmt = Helper::checkImageDailyLimit();
        if ($chkLmt->getStatusCode() === 429) {
            return $chkLmt;
        }
        $history = $request->input('chatHistory');
        $client = OpenAI::factory()
            ->withApiKey($apiKey)
            ->withHttpClient(new \GuzzleHttp\Client)
            ->make();
        $completion = $client->chat()->create([
            'model'    => $driver->enum()->value,
            'messages' => [[
                'role'    => 'user',
                'content' => "Write what does user want to draw at the last moment of chat history. \n\n\nChat History: $history \n\n\n\n Result is 'Draw an image of ... ",
            ]],
        ]);

        $nameOfImage = Str::random(12) . '.png';
        $modelImage = $this->getDefaultOpenAiImageModel();
        $driver = Entity::driver($modelImage)->inputImageCount(1)->calculateCredit();
        $driver->redirectIfNoCreditBalance();
        $response = FacadesOpenAI::images()->create([
            'model'           => $modelImage->value,
            'prompt'          => $completion->choices[0]->message->content,
            'size'            => '1024x1024',
            'response_format' => 'b64_json',
        ]);
        $image_url = $response['data'][0]['b64_json'];
        $contents = base64_decode($image_url);
        // save file on local storage or aws s3
        Storage::disk('public')->put($nameOfImage, $contents);

        $path = '/uploads/' . $nameOfImage;

        if ($this->settings_two->ai_image_storage === self::STORAGE_S3) {

            try {
                $uploadedFile = new File(substr($path, 1));

                $aws_path = Storage::disk(self::STORAGE_S3)->put('', $uploadedFile);
                unlink(substr($path, 1));
                $path = Storage::disk(self::STORAGE_S3)->url($aws_path);
            } catch (Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
            }
        }
        Usage::getSingle()->updateImageCounts($driver->calculate());
        $driver->decreaseCredit();

        return response()->json(['path' => $path]);
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function imageOutput($param, $post, $user): JsonResponse
    {
        $lockKey = 'generate_image_output_lock';
        if (! Cache::lock($lockKey, 10)->get()) { // Attempt to acquire lock
            return response()->json(['message' => 'Image generation in progress. Please try again later.'], 409);
        }

        $engineCheck = match ($param['image_generator']) {
            'flux-pro', 'ideogram' => EngineEnum::FAL_AI->value,
            EntityEnum::MIDJOURNEY->value => EngineEnum::PI_API->value,
            default                       => $param['image_generator'],
        };

        try {
            $engine = EngineEnum::fromSlug($engineCheck);
            $model = $this->getDefaultModel($engine);
            $code = $this->getEngineCode($engine);
            $number_of_images = (int) $param['image_number_of_images'];

            if ($param['image_generator'] === 'ideogram') {
                $model = EntityEnum::IDEOGRAM;
            }

            $driver = Entity::driver($model)->inputImageCount($number_of_images)->calculateCredit();

            $chkLmt = Helper::checkImageDailyLimit();

            if ($chkLmt->getStatusCode() === 429) {
                return $chkLmt;
            }

            $driver->redirectIfNoCreditBalance();

            $apiKey = $this->getOpenAiApiKey($user);

            config(['openai.api_key' => $apiKey]);

            set_time_limit(120);

            $entries = [];
            for ($i = 0; $i < $number_of_images; $i++) {
                $imageDetails = $this->processImageGeneration($engine, $model, $param);
                $savePath = $this->saveImageOutputToStorage($imageDetails);
                $entry = $this->saveEntryToDatabase($imageDetails, $user, $post, $code, $savePath, $model);
                $entry->img_id = 'img-' . $entry->response . '-' . $entry->id;
                $entries[] = $entry;
            }

            Usage::getSingle()->updateImageCounts($driver->calculate());
            $driver->decreaseCredit();

            Cache::lock($lockKey)->release();

            return response()->json([
                'status'        => 'success',
                'images'        => $entries,
                'nameOfImage' 	 => $imageDetails['nameOfImage'],
                'image_storage' => $this->settings_two->ai_image_storage,
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        } finally {
            Cache::lock($lockKey)->forceRelease();
        }
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function videoOutput($param): JsonResponse
    {
        set_time_limit(120);
        $imageToVideoModel = EntityEnum::IMAGE_TO_VIDEO;
        $driver = Entity::driver($imageToVideoModel)->inputVideoCount(1)->calculateCredit();
        $driver->redirectIfNoCreditBalance();

        $init_image = file_get_contents($param['image_src']);
        $nameOfImage = Str::random(12) . '.png';

        Storage::disk('public')->put($nameOfImage, $init_image);
        $path = '/uploads/' . $nameOfImage;
        if ($this->settings_two->ai_image_storage === self::STORAGE_S3) {
            try {
                $uploadedFile = new File(substr($path, 1));

                $aws_path = Storage::disk(self::STORAGE_S3)->put('', $uploadedFile);
                unlink(substr($path, 1));
                $path = Storage::disk(self::STORAGE_S3)->url($aws_path);
            } catch (Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
            }
        }
        $seed = $param['seed'];
        $cfg_scale = $param['cfg_scale'];
        $motion_bucket_id = $param['motion_bucket_id'];

        $stableDiffusionKey = $this->getStableApiKey();
        if (empty($stableDiffusionKey)) {
            return response()->json(['status' => 'error', 'message' => __('You must provide a StableDiffusion API Key.')]);
        }

        $client = new Client([
            'base_uri' => 'https://api.stability.ai/v2beta/',
            'headers'  => [
                'content-type'  => 'multipart/form-data',
                'Authorization' => 'Bearer ' . $stableDiffusionKey,
            ],
        ]);

        $payload = [
            'image'            => $init_image,
            'cfg_scale'        => $cfg_scale,
            'seed'             => $seed,
            'motion_bucket_id' => $motion_bucket_id,
        ];

        $multipart = [];
        foreach ($payload as $key => $value) {
            if ($key === 'image') {
                $multipart[] = ['name' => $key, 'contents' => $value, 'filename' => 'image.png'];
            } else {
                $multipart[] = ['name' => $key, 'contents' => $value];
            }
        }
        $payload = $multipart;

        try {
            $response = $client->post($imageToVideoModel->value, [
                'multipart' => $payload,
            ]);
        } catch (RequestException|Exception $e) {
            if ($e->hasResponse()) {
                $errorMessage = $e->getResponse()->getBody()->getContents();
                $errorData = json_decode($errorMessage, true, 512, JSON_THROW_ON_ERROR);

                $error = $errorData['message'] ?? $errorMessage;

                throw new RuntimeException($error);
            }

            throw new RuntimeException($e->getMessage());
        }

        $body = $response->getBody();
        if ($response->getStatusCode() === 200) {
            $driver->decreaseCredit();

            return response()->json(['status' => 'success', 'id' => json_decode($body, false, 512, JSON_THROW_ON_ERROR)->id, 'sourceUrl' => $path]);
        }

        if ($body->status === 'error') {
            $message = $body->message;
        } else {
            $message = __('Failed, Try Again');
        }

        return response()->json(['status' => 'error', 'message' => $message]);
    }

    /**
     * @throws guzzleException
     *                         this function only for check progress, no need to decrease the credit
     */
    public function checkVideoProgress(Request $request): JsonResponse
    {
        $resultId = $request->id;
        $user = Auth::user();
        $stableDiffusionKey = $this->getStableApiKey();
        $client = new Client([
            'base_uri' => 'https://api.stability.ai/v2beta/image-to-video/result/' . $resultId,
            'headers'  => [
                'Accept'        => 'video/*',
                'Authorization' => 'Bearer ' . $stableDiffusionKey,
            ],
        ]);

        try {
            $response = $client->request('GET');
            if ($response->getStatusCode() === 200) {
                $fileContents = $response->getBody()->getContents();
                $nameOfImage = 'image-to-video-' . Str::random(12) . '.mp4';
                Storage::disk('public')->put($nameOfImage, $fileContents);
                $path = 'uploads/' . $nameOfImage;
                $imageStorage = $this->settings_two->ai_image_storage;
                if ($imageStorage === self::STORAGE_S3) {
                    try {
                        $uploadedFile = new File($path);
                        $aws_path = Storage::disk(self::STORAGE_S3)->put('', $uploadedFile);
                        unlink($path);
                        $path = Storage::disk(self::STORAGE_S3)->url($aws_path);
                    } catch (Exception $e) {
                        return response()->json(['status' => 'error', 'message' => 'AWS Error - ' . $e->getMessage()]);
                    }
                }
                $entry = UserOpenai::create([
                    'team_id'   => $user->team_id,
                    'title'     => __('New Video'),
                    'slug'      => Str::random(7) . Str::slug($user?->fullName()) . '-workbsook',
                    'user_id'   => Auth::id(),
                    'openai_id' => OpenAIGenerator::where('slug', 'ai_video')->first()->id,
                    'input'     => $request->url,
                    'response'  => 'VIDEO',
                    'output'    => $imageStorage === self::STORAGE_S3 ? $path : '/' . $path,
                    'hash'      => Str::random(256),
                    'credits'   => 5,
                    'words'     => 0,
                    'storage'   => $imageStorage === self::STORAGE_S3 ? UserOpenai::STORAGE_AWS : UserOpenai::STORAGE_LOCAL,
                    'payload'   => request()?->all(),
                ]);

                return response()->json(['status' => 'finished', 'url' => $path, 'video' => $entry]);
            }

            if ($response->getStatusCode() === 202) {
                return response()->json(['success', 'status' => 'in-progress']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }

        return response()->json(['status' => 'error', 'message' => __('Failed, Try Again')]);
    }

    /**
     * @throws Throwable
     * @throws JsonException
     */
    public function audioOutput($file, $post, $user): JsonResponse
    {
        $driver = Entity::driver(EntityEnum::WHISPER_1);
        $driver->redirectIfNoCreditBalance();
        $path = 'uploads/audio/';
        $file_name = Str::random(4) . '-' . Str::slug($user?->fullName()) . '-audio.' . $file->getClientOriginalExtension();

        // Audio Extension Control
        $audioTypes = ['mp3', 'mp4', 'mpeg', 'mpga', 'm4a', 'wav', 'webm'];
        if (! in_array(Str::lower($file->getClientOriginalExtension()), $audioTypes)) {
            $data = [
                'errors' => ['Invalid extension, accepted extensions are mp3, mp4, mpeg, mpga, m4a, wav, and webm.'],
            ];

            return response()->json($data, 419);
        }

        $file->move($path, $file_name);
        $fullPath = $path . $file_name;

        $response = FacadesOpenAI::audio()->transcribe([
            'file'            => fopen($fullPath, 'rb'),
            'model'           => EntityEnum::WHISPER_1->value,
            'response_format' => 'verbose_json',
        ]);

        $text = $response->text;

        UserOpenai::create([
            'team_id'   => $user->team_id,
            'title'     => request('title') ?: null,
            'slug'      => Str::random(7) . Str::slug($user?->fullName()) . '-speech-to-text-workbook',
            'user_id'   => $user->id,
            'openai_id' => $post->id,
            'input'     => $fullPath,
            'response'  => json_encode($response->toArray(), JSON_THROW_ON_ERROR),
            'output'    => $text,
            'hash'      => Str::random(256),
            'credits'   => countWords($text),
            'words'     => countWords($text),
        ]);

        $driver->input($text)->calculateCredit()->decreaseCredit();
        Usage::getSingle()->updateWordCounts($driver->calculate());

        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $post->id)->orderBy('created_at', 'desc')->get();
        $openai = OpenAIGenerator::find($post->id);
        $html2 = view('panel.user.openai.components.generator_sidebar_table', compact('userOpenai', 'openai'))->render();

        return response()->json(compact('html2'));
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Throwable
     * @throws JsonException
     */
    public function audioIsolator($file, $post, $user): JsonResponse
    {
        $voiceTypes = ['ogg', 'mp3', 'mp4', 'mpeg', 'mpga', 'm4a', 'wav', 'webm'];
        if (! in_array(Str::lower($file->getClientOriginalExtension()), $voiceTypes)) {
            $data = [
                'errors' => ['Invalid extension, accepted extensions are mp3, mp4, mpeg, mpga, m4a, wav, and webm.'],
            ];

            return response()->json($data, 419);
        }

        set_time_limit(3000);
        $driver = Entity::driver(EntityEnum::ISOLATOR);
        $driver->redirectIfNoCreditBalance();

        $apiKey = $this->settings_two->elevenlabs_api_key;
        if (empty($apiKey)) {
            return response()->json([
                'type'   => 'error',
                'errors' => [__('Elevenlabs API key is required.')],
            ], 401);
        }

        $mp3File = $file->getRealPath();
        $mp3FileName = $file->getClientOriginalName();
        $client = new Client;
        $response = $client->request('POST', 'https://api.elevenlabs.io/v1/audio-isolation', [
            'headers' => [
                'xi-api-key' => $apiKey,
            ],
            'multipart' => [
                [
                    'name'     => 'audio',
                    'contents' => fopen($mp3File, 'rb'),
                    'filename' => $mp3FileName,
                ],
            ],
            'timeout' => 3000,
        ]);
        $resAudio = $response->getBody();
        $characterCost = $response->getHeader('character-cost');
        $characters = max($characterCost[0], 0);

        $driver->input($characters)->calculateCredit()->decreaseCredit();
        Usage::getSingle()->updateWordCounts($driver->calculate());

        $audioName = $user->id . '-' . Str::random(20) . '.mp3';
        Storage::disk('public')->put($audioName, $resAudio);

        $langsAndVoices['language'][] = 'en-US';
        $langsAndVoices['voices'][] = $audioName;

        UserOpenai::create([
            'team_id'   => $user->team_id,
            'output'    => $audioName,
            'input'     => $audioName,
            'title'     => request('title') ?: __('Isolated Voice'),
            'openai_id' => $post->id,
            'slug'      => Str::random(20) . Str::slug($user?->fullName()) . '-isolated-voice',
            'user_id'   => $user->id,
            'hash'      => Str::random(256),
            'credits'   => $characters,
            'response'  => json_encode($langsAndVoices, JSON_THROW_ON_ERROR),
        ]);
        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $post->id)->orderBy('created_at', 'desc')->paginate(10);
        $userOpenai->withPath(route('dashboard.user.openai.generator', 'ai_voice_isolator'));
        $openai = OpenAIGenerator::where('id', $post->id)->first();
        $html2 = view('panel.user.openai.components.generator_sidebar_table', compact('userOpenai', 'openai'))->render();

        return response()->json(compact('html2'));
    }

    public function messageTitleSave(Request $request): JsonResponse
    {
        if (! $request['message_id'] || ! $request['title']) {
            return response()->json([
                'message' => trans('TItle is required'),
            ]);
        }

        $entry = UserOpenai::find($request->message_id);
        $entry->title = request('title');
        $entry->save();

        return response()->json([
            'message' => trans('Title updated'),
        ]);
    }

    /**
     * @throws Exception
     */
    public function lowGenerateSave(Request $request): JsonResponse
    {
        $user = Auth::user();
        $response = $request->response;
        $total_user_tokens = countWords($response);

        $entry = UserOpenai::find($request->message_id);
        if (! $entry) {
            $entry = UserOpenai::create([
                'user_id'   => $user?->id,
                'input'     => $response,
                'hash'      => str()->random(256),
                'team_id'   => $user?->team_id,
                'slug'      => str()->random(7) . str($user?->fullName())->slug() . '-workbook',
                'openai_id' => $request->openai_id ?? 1,
            ]);
        }
        $entry->title = request('title') ?: null;
        $entry->credits = $total_user_tokens;
        $entry->words = $total_user_tokens;
        $entry->response = $response;
        $entry->output = $response;
        $entry->save();

        $reSave = $request->boolean('resave', false);
        if (! $reSave) {
            Entity::driver()->input($response)->calculateCredit()->decreaseCredit();
        }

        return response()->json(['status' => 'success']);
    }

    public function lazyLoadImage(Request $request): JsonResponse
    {
        $items_per_page = 5;
        $offset = $request->get('offset', 0);
        $post_type = $request->get('post_type');
        $post = OpenAIGenerator::where('slug', $post_type)->first();

        $all_images = UserOpenai::where('user_id', Auth::id())
            ->where('openai_id', $post->id);

        $all_images_count = $all_images->count();
        $current_images_list = $all_images->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($items_per_page)
            ->get();
        $thumbnails = [];
        foreach ($current_images_list as $image) {
            // Generate thumbnail URL using your existing method
            $thumbnailUrl = ThumbImage($image->output);
            // Append the image object with thumbnail URL to the array
            $imageWithThumbnail = $image->toArray(); // Convert the image object to an array
            $imageWithThumbnail['thumbnail'] = $thumbnailUrl; // Add thumbnail URL to the array
            $thumbnails[] = $imageWithThumbnail; // Append the modified array to the thumbnails array
        }

        return response()->json([
            'images'          => $thumbnails,
            'count_current'   => $current_images_list->count() + $offset,
            'count_remaining' => $all_images_count - ($current_images_list->count() + $offset),
            'count_all'       => $all_images_count,
        ]);
    }

    public function updateWriting(Request $request)
    {
        $driver = Entity::driver();
        $driver->redirectIfNoCreditBalance();

        $user = $request->user();

        $content = $request->get('content');
        $prompt = $request->prompt;

        if (empty($content)) {
            return response()->json(['result' => '']);
        }

        ApiHelper::setOpenAiKey();
        $detect = FacadesOpenAI::chat()->create([
            'model'    => $driver->enum()->value,
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'You are a helpful language detection assistant. Must detect the content language and return it only ' . app()->getLocale() . ' language code. For example: "en_US"',
                ],
                [
                    'role'    => 'user',
                    'content' => "$prompt\n\nContent: \"$content\"",
                ],
            ],
        ]);

        $languageCode = $detect->choices[0]->message->content;

        $completion = FacadesOpenAI::chat()->create([
            'model'    => $driver->enum()->value,
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'You are a helpful assistant. You must response only with answer. Response language must be: ' . $languageCode,
                ],
                [
                    'role'    => 'user',
                    'content' => "$prompt\n\nContent: \"$content\"",
                ],
            ],
        ]);

        $content = $completion->choices[0]->message->content;
        $driver->input($content)->calculateCredit()->decreaseCredit();
        Usage::getSingle()->updateWordCounts($driver->calculate());

        return response()->json(['result' => $completion->choices[0]->message->content]);
    }

    public function reWrite()
    {
        $user = Auth::user();
        $apiUrl = base64_encode('https://api.openai.com/v1/chat/completions');
        if ($this->settings_two->openai_default_stream_server == 'backend') {
            $apikeyPart1 = base64_encode(rand(1, 100));
            $apikeyPart2 = base64_encode(rand(1, 100));
            $apikeyPart3 = base64_encode(rand(1, 100));
        } else {
            $apiKey = $this->getOpenAiApiKey($user);
            $len = strlen($apiKey);
            $len = max($len, 6);
            $parts[] = substr($apiKey, 0, $l[] = rand(1, $len - 5));
            $parts[] = substr($apiKey, $l[0], $l[] = rand(1, $len - $l[0] - 3));
            $parts[] = substr($apiKey, array_sum($l));
            $apikeyPart1 = base64_encode($parts[0]);
            $apikeyPart2 = base64_encode($parts[1]);
            $apikeyPart3 = base64_encode($parts[2]);
        }

        return view('panel.user.openai.rewriter.index', compact(
            'apikeyPart1',
            'apikeyPart2',
            'apikeyPart3',
            'apiUrl',
        ));
    }

    public function getYoutubeCaptions(Request $request): JsonResponse|bool|array|string
    {
        $vidURL = $request->video_url;

        return (new youtubeTranscriptService)->getTranscript($vidURL);
    }

    private function getOpenAiApiKey(?User $user): string
    {
        return ApiHelper::setOpenAiKey();
    }

    private function getStableApiKey(): string
    {
        $stableDiffusionKeys = explode(',', $this->settings_two->stable_diffusion_api_key);

        return $stableDiffusionKeys[array_rand($stableDiffusionKeys)];
    }

    /**
     * Get the default model based on the AI engine.
     *
     * @throws Exception
     */
    private function getDefaultModel(?EngineEnum $engine): ?EntityEnum
    {
        return match ($engine) {
            EngineEnum::OPEN_AI          => $this->getDefaultOpenAiImageModel(),
            EngineEnum::STABLE_DIFFUSION => $this->getStableDiffusionDefaultModel(),
            EngineEnum::FAL_AI           => $this->getDefaultFalAiModel(),
            EngineEnum::PI_API           => EntityEnum::MIDJOURNEY,
            default                      => throw new Exception(__('Invalid AI Engine')),
        };
    }

    /**
     * Process image generation based on the AI engine.
     *
     * @throws Exception|GuzzleException
     */
    private function processImageGeneration(?EngineEnum $engine, ?EntityEnum $model, array $param): array
    {
        return match ($engine) {
            EngineEnum::OPEN_AI          => $this->processOpenAIImage($model, $param),
            EngineEnum::STABLE_DIFFUSION => $this->processStableDiffusionImage($model, $param),
            EngineEnum::FAL_AI           => $this->processFalAIImage($model, $param),
            EngineEnum::PI_API           => $this->processPiAPIImage($model, $param),
            default                      => throw new Exception(__('Invalid AI Engine')),
        };
    }

    private function processOpenAIImage(?EntityEnum $model, array $param): array
    {
        $is_demo = Helper::appIsDemo();
        $size = $param['size'];
        $description = $param['description'];
        $style = $param['image_style'] ?? null;
        $lighting = $param['image_lighting'] ?? null;
        $mood = $param['mood'] ?? null;
        $quality = $param['quality'];
        $prompt = $description;
        if (is_null($prompt)) {
            throw new RuntimeException(__('You must provide a prompt'));
        }
        $attributes = [
            'style'    => $style ? "$style style" : null,
            'lighting' => $lighting ? "$lighting lighting" : null,
            'mood'     => $mood ? "$mood mood" : null,
        ];
        $prompt .= ' ' . implode(' ', array_filter($attributes));
        $response = FacadesOpenAI::images()->create([
            'model'           => $model,
            'prompt'          => $prompt,
            'size'            => $is_demo ? $this->getDemoImageSize($model) : $size,
            'response_format' => 'b64_json',
            'quality'         => $is_demo ? 'standard' : $quality,
            'n'               => 1,
        ]);
        $contents = base64_decode($response['data'][0]['b64_json']);
        $nameOfImage = Str::random(12) . '-DALL-E-' . Str::slug(explode(' ', mb_substr($prompt, 0, 15))[0]) . '.png';

        return [
            'prompt'                => $prompt,
            'imageContent'          => $contents,
            'nameOfImage'           => $nameOfImage,
        ];
    }

    /**
     * @throws GuzzleException
     * @throws RandomException
     * @throws JsonException
     */
    private function processStableDiffusionImage(?EntityEnum $model, array $param): array
    {
        $stable_type = $param['type'];
        $prompt = $param['stable_description'];
        if (is_null($prompt)) {
            throw new RuntimeException(__('You must provide a prompt'));
        }

        $negative_prompt = $param['negative_prompt'];
        $style_preset = $param['style_preset'];
        $sampler = $param['sampler'];
        $clip_guidance_preset = $param['clip_guidance_preset'];
        $image_resolution = $param['image_resolution'];
        $init_image = $param['image_src'] ?? null;
        $mood = $param['mood'] ?? null;
        $defaultSdModel = $this->getStableDiffusionDefaultModel()->value;
        $isV2BetaModels = EntityEnum::fromSlug($defaultSdModel)->isV2BetaSdEntity();

        $width = (int) explode('x', $image_resolution)[0];
        $height = (int) explode('x', $image_resolution)[1];

        if ($defaultSdModel === BedrockEngine::BEDROCK->value && $stable_type === 'text-to-image') {
            $response = $this->bedrockService->invokeStableDiffusion($prompt, random_int(1, 1000000), $width, $height);
            $nameOfImage = Str::random(12) . '-AWS-SD-' . Str::slug(explode(' ', mb_substr($prompt, 0, 15))[0]) . '.png';

            return [
                'prompt'                => $prompt,
                'imageContent'          => $response,
                'nameOfImage'           => $nameOfImage,
            ];
        }
        $stableDiffusionKey = $this->getStableApiKey();
        if (empty($stableDiffusionKey)) {
            throw new RuntimeException(__('You must provide a StableDiffusion API Key.'));
        }

        $sd3Payload = [];
        $baseUri = $isV2BetaModels && in_array($stable_type, ['text-to-image', 'image-to-image'], true)
            ? 'https://api.stability.ai/v2beta/stable-image/generate/'
            : 'https://api.stability.ai/v1/generation/';
        $contentType = ($stable_type === 'image-to-image') ? 'multipart/form-data' : 'application/json';
        $client = new Client([
            'base_uri' => $baseUri,
            'headers'  => [
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $stableDiffusionKey,
                'Accept'        => 'application/json',
            ],
        ]);
        $payload = [
            'cfg_scale'            => 7,
            'clip_guidance_preset' => $clip_guidance_preset ?? 'NONE',
            'samples'              => 1,
            'steps'                => 50,
        ];
        if ($sampler) {
            $payload['sampler'] = $sampler;
        }
        if ($style_preset) {
            $payload['style_preset'] = $style_preset;
        }
        $content_type = 'json';
        switch ($stable_type) {
            case 'multi-prompt':
                $stable_url = 'text-to-image';
                $payload['width'] = $width;
                $payload['height'] = $height;
                $arr = [];
                foreach ($prompt as $p) {
                    $arr[] = [
                        'text'   => $p . ($mood === null ? '' : (' ' . $mood . ' mood.')),
                        'weight' => 1,
                    ];
                }
                $prompt = $arr;
                $payload['text_prompts'] = $prompt;

                break;
            case 'image-to-image':
                $content_type = 'multipart';
                $stable_url = $stable_type;
                $payload['init_image'] = $init_image->get();
                $sd3Payload = [
                    [
                        'name'     => 'prompt',
                        'contents' => $prompt,
                    ],
                    [
                        'name'     => 'mode',
                        'contents' => 'image-to-image',
                    ],
                    [
                        'name'     => 'strength',
                        'contents' => 0,
                    ],
                    [
                        'name'     => 'image',
                        'contents' => $init_image->get(),
                        'filename' => $init_image->getClientOriginalName(),
                    ],
                ];
                $prompt = [
                    [
                        'text'   => $prompt . ($mood === null ? '' : (' ' . $mood . ' mood.')),
                        'weight' => 1,
                    ],
                ];
                $payload['text_prompts'] = $prompt;

                break;
            default:
                $stable_url = $stable_type;
                $payload['width'] = $width;
                $payload['height'] = $height;
                $sd3Payload = [
                    [
                        'name'     => 'prompt',
                        'contents' => $prompt,
                    ],
                    [
                        'name'     => 'file',
                        'contents' => 'no',
                    ],
                    [
                        'name'     => 'output_format',
                        'contents' => 'png',
                    ],
                ];
                $prompt = [
                    [
                        'text'   => $prompt . ($mood === null ? '' : (' ' . $mood . ' mood.')),
                        'weight' => 1,
                    ],
                ];
                $payload['text_prompts'] = $prompt;

                break;
        }
        if ($negative_prompt) {
            $prompt[] = ['text' => $negative_prompt, 'weight' => -1];
        }
        if ($content_type === 'multipart') {
            $multipart = [];
            foreach ($payload as $key => $value) {
                if (! is_array($value)) {
                    $multipart[] = ['name' => $key, 'contents' => $value];

                    continue;
                }
                foreach ($value as $multiKey => $multiValue) {
                    $multiName = $key . '[' . $multiKey . ']' . (is_array($multiValue) ? '[' . key($multiValue) . ']' : '') . '';
                    $multipart[] = ['name' => $multiName, 'contents' => (is_array($multiValue) ? reset($multiValue) : $multiValue)];
                }
            }
            $payload = $multipart;
        }

        try {
            if ($isV2BetaModels && in_array($stable_type, ['text-to-image', 'image-to-image'], true)) {
                $defaultSdModel = 'sd3';
                $sd3Payload[] = ['name' => 'model', 'contents' => $defaultSdModel];
                $sd3Payload[] = [
                    'name'     => 'aspect_ratio',
                    'contents' => $width . ':' . $height,
                ];

                $response = $client->post($defaultSdModel, [
                    'headers'   => ['accept' => 'application/json'],
                    'multipart' => $sd3Payload,
                ]);

            } elseif ($stable_type === 'upscale') {
                set_time_limit(500);
                $http = new Client([
                    'headers'  => [
                        'Content-Type'  => $contentType,
                        'Authorization' => 'Bearer ' . $stableDiffusionKey,
                        'Accept'        => 'application/json',
                    ],
                ]);
                $response = $http->post('https://api.stability.ai/v2beta/stable-image/upscale/fast', [
                    'multipart' => [
                        [
                            'name'     => 'image',
                            'contents' => $init_image->get(),
                            'filename' => $init_image->getClientOriginalName(),
                        ],
                        [
                            'name'     => 'output_format',
                            'contents' => 'png',
                        ],
                    ],
                ]);
            } else {
                $defaultSdModel = $stable_type === 'multi-prompt' ? EntityEnum::STABLE_DIFFUSION_V_1_6->value : $defaultSdModel;
                $response = $client->post($defaultSdModel . '/' . $stable_url, [$content_type => $payload]);
            }
        } catch (Exception $e) {
            if ($e->hasResponse()) {
                $errorMessage = $e->getResponse()->getBody()->getContents();
                $errorData = json_decode($errorMessage, true, 512, JSON_THROW_ON_ERROR);
                $errorDetails = $errorData['errors'][0] ?? 'Unknown error';

                throw new RuntimeException($errorDetails);
            }

            throw new RuntimeException($e->getMessage());
        }

        $body = $response->getBody();
        if ($response->getStatusCode() === 200) {
            $nameOfPrompt = explode(' ', mb_substr($prompt[0]['text'], 0, 15))[0];
            $nameOfImage = Str::random(12) . '-STABLE-' . $nameOfPrompt . '.png';
            if (
                ($stable_type === 'text-to-image' || $stable_type === 'image-to-image' || $stable_type === 'upscale') && $isV2BetaModels
            ) {
                $contents = base64_decode(json_decode($body, false, 512, JSON_THROW_ON_ERROR)->image);
            } else {
                $contents = base64_decode(json_decode($body, false, 512, JSON_THROW_ON_ERROR)->artifacts[0]->base64);
            }
        } else {
            if ($body->status === 'error') {
                $message = $body->message;
            } else {
                $message = __('Failed, Try Again');
            }

            throw new RuntimeException($message);
        }

        return [
            'prompt'                => $prompt[0]['text'],
            'imageContent'          => $contents,
            'nameOfImage'           => $nameOfImage,
        ];
    }

    private function processPiAPIImage(?EntityEnum $model, array $param): array
    {
        $prompt = $param['description_midjourney'];
        if (is_null($prompt)) {
            throw new RuntimeException(__('You must provide a prompt'));
        }

        $requestId = PiAPIService::generate($prompt);

        return [
            'engine'                => EngineEnum::PI_API,
            'requestId'             => $requestId,
            'status'                => 'IN_QUEUE',
            'output'                => asset(self::LOADING_GIF),
            'prompt'                => $prompt,
            'imageContent'          => $prompt,
            'nameOfImage'           => Str::random(12) . '-MIDJOURNEY-' . Str::slug(explode(' ', mb_substr($prompt, 0, 15))[0]) . '.png',
        ];
    }

    private function processFalAIImage(?EntityEnum $model, array $param): array
    {

        if ($param['image_generator'] === 'ideogram') {
            $prompt = $param['description_ideogram'];
        } else {
            $prompt = $param['description_flux_pro'];
        }

        if (is_null($prompt)) {
            throw new RuntimeException(__('You must provide a prompt'));
        }
        if ($model === EntityEnum::IDEOGRAM) {
            $requestId = FalAIService::ideogramGenerate($prompt);
        } else {
            $requestId = FalAIService::generate($prompt, $model);
        }

        return [
            'engine'                => EngineEnum::FAL_AI,
            'requestId'             => $requestId,
            'status'                => 'IN_QUEUE',
            'output'                => asset(self::LOADING_GIF),
            'prompt'                => $prompt,
            'imageContent'          => null,
            'nameOfImage'           => Str::random(12) . '-FLUX-' . Str::slug(explode(' ', mb_substr($prompt, 0, 15))[0]) . '.png',
        ];
    }

    private function saveEntryToDatabase(array $imageDetails, User $user, OpenAIGenerator $post, string $code, string $savePath, ?EntityEnum $model): UserOpenai
    {
        $payload = request()?->all();
        $payload['model'] = $model?->value;

        $data = [
            'team_id'   => $user->team_id,
            'title'     => $imageDetails['prompt'] ? Str::limit($imageDetails['prompt'], 40) : $imageDetails['nameOfImage'],
            'slug'      => Str::random(7) . Str::slug($user?->fullName()) . '-workbook',
            'user_id'   => $user->id,
            'openai_id' => $post->id,
            'input'     => $imageDetails['prompt'],
            'response'  => $code,
            'output'    => ThumbImage(url($savePath)),
            'hash'      => Str::random(256),
            'credits'   => 1,
            'words'     => 0,
            'storage'   => $this->settings_two->ai_image_storage,
            'payload'   => $payload,
        ];
        if (isset($imageDetails['engine']) && ($imageDetails['engine'] === EngineEnum::FAL_AI || $imageDetails['engine'] === EngineEnum::PI_API)) {
            $data['request_id'] = $imageDetails['requestId'];
            $data['status'] = $imageDetails['status'];
            $data['output'] = $imageDetails['output'];
        }

        return UserOpenai::create($data);
    }

    private function saveImageOutputToStorage(array $imageDetails): string
    {
        if (isset($imageDetails['engine']) && ($imageDetails['engine'] === EngineEnum::FAL_AI || $imageDetails['engine'] === EngineEnum::PI_API)) {
            return '/';
        }

        $image_storage = $this->settings_two->ai_image_storage;

        return match ($image_storage) {
            self::STORAGE_S3    => $this->saveImageToS3($imageDetails),
            self::CLOUDFLARE_R2 => $this->saveImageToR2($imageDetails),
            default             => $this->saveImageToLocal($imageDetails),
        };
    }

    private function saveImageToS3(array $imageDetails): string
    {
        $localPath = $this->saveImageToLocal($imageDetails);

        try {
            $uploadedFile = new File($localPath);
            $aws_path = Storage::disk('s3')->put('', $uploadedFile);
            $fullAWSPath = Storage::disk('s3')->url($aws_path);
        } catch (Exception $e) {
            throw new RuntimeException('AWS Error - ' . $e->getMessage());
        }

        return $fullAWSPath;
    }

    private function saveImageToLocal(array $imageDetails): string
    {
        Storage::disk('public')->put($imageDetails['nameOfImage'], $imageDetails['imageContent']);

        return 'uploads/' . $imageDetails['nameOfImage'];
    }

    private function saveImageToR2(array $imageDetails): string
    {
        $this->saveImageToLocal($imageDetails);

        Storage::disk('r2')->put($imageDetails['nameOfImage'], $imageDetails['imageContent']);

        return Storage::disk('r2')->url($imageDetails['nameOfImage']);
    }

    private function getEngineCode(?EngineEnum $engine): string
    {
        return match ($engine) {
            EngineEnum::STABLE_DIFFUSION => 'SD',
            EngineEnum::FAL_AI           => 'FL',
            EngineEnum::PI_API           => 'PI',
            default                      => 'DE',
        };
    }

    private function getDemoImageSize(?EntityEnum $model): string
    {
        return match ($model) {
            EntityEnum::DALL_E_3 => '1024x1024',
            EntityEnum::DALL_E_2 => '256x256',
            default              => '512x512',
        };
    }

    private function getDefaultOpenAiImageModel(): EntityEnum
    {
        $default = match ($this->settings_two->dalle) {
            'dalle3' => EntityEnum::DALL_E_3->slug(),
            'dalle2' => EntityEnum::DALL_E_2->slug(),
            default  => $this->settings_two->dalle,
        };

        return EntityEnum::fromSlug($default) ?? EntityEnum::DALL_E_2;
    }

    private function getStableDiffusionDefaultModel(): EntityEnum
    {
        return EntityEnum::fromSlug($this->settings_two?->stablediffusion_default_model) ?? EntityEnum::SD_3;
    }

    private function getDefaultFalAiModel(): EntityEnum
    {
        return EntityEnum::fromSlug(setting('fal_ai_default_model', EntityEnum::FLUX_PRO->value)) ?? EntityEnum::FLUX_PRO;
    }

    private function applyPromptRules(string $prompt): string
    {
        $prompt .= '
        here some rules you must follow:
            1. Dont use double quotation ".." marks in answers.
        ';

        return $prompt;
    }
}
