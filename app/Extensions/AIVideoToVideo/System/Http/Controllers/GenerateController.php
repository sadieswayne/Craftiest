<?php

namespace App\Extensions\AIVideoToVideo\System\Http\Controllers;

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Extensions\AIVideoToVideo\System\Services\AnimatediffV2vService;
use App\Extensions\AIVideoToVideo\System\Services\BaseService;
use App\Extensions\AIVideoToVideo\System\Services\Cogvideox5bService;
use App\Extensions\AIVideoToVideo\System\Services\FastAnimatediffTurboService;
use App\Extensions\AIVideoToVideo\System\Services\VideoUpscalerService;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserOpenai;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GenerateController extends Controller
{
    /**
     * Check the status for all records with the given openai_id and status.
     */
    public function checkedAll(Request $request): void
    {
        $openaiId = $request->input('openai_id');

        UserOpenai::query()
            ->whereNotNull('request_id')
            ->where('openai_id', $openaiId)
            ->where('status', 'IN_QUEUE')
            ->get()
            ->each(function (UserOpenai $openai) {
                $this->getService($openai->payload['model'])
                    ->setOpenai($openai)
                    ->checked();
            });
    }

    /**
     * Start the video generation process.
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules($request));

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        /** @var EntityEnum $entity */
        $entity = EntityEnum::fromSlug($request['model']);

        $driver = Entity::driver($entity)
            ->inputVideoCount(1)
            ->calculateCredit();

        try {
            $driver->redirectIfNoCreditBalance();
        } catch (Exception $e) {
            return response([
                'message' => 'You have no credits left. Please consider upgrading your plan.',
                'type'    => 'error',
            ], 422);
        }

        // Execute operations within a single transaction
        DB::beginTransaction();

        try {
            $entry = $this->createEntry($request, $entity);

            $this->getService($entity)
                ->setOpenai($entry)
                ->generate();

            $driver->decreaseCredit();

            DB::commit();

            return $entry;
        } catch (Exception $e) {
            DB::rollBack();

            // Optionally, log the error here
            return response([
                'message' => 'An error occurred during video generation.',
                'type'    => 'error',
            ], 500);
        }
    }

    /**
     * Create a new UserOpenai record.
     */
    protected function createEntry(Request $request, EntityEnum $entity): UserOpenai
    {
        $user = $request->user();

        // Process the file upload if available
        $videoPath = $request->hasFile('video')
            ? $request->file('video')->store('videos', ['disk' => 'public'])
            : null;

        $payload = [
            'model'                  => $entity->value,
            'scale'                  => $request->input('scale'),
            'prompt'                 => $request->input('prompt'),
            'negative_prompt'        => $request->input('negative_prompt'),
            'inference_steps'        => $request->input('inference_steps'),
            'every_nth_frame'        => $request->input('every_nth_frame'),
            'first_n_seconds'        => $request->input('first_n_seconds'),
            'num_inference_steps'    => $request->input('num_inference_steps'),
            'select_every_nth_frame' => $request->input('select_every_nth_frame'),
            'video'                  => $videoPath,
        ];

        $data = [
            'user_id'   => $user->id,
            'input'     => $request->input('prompt', 'Video to video'),
            'hash'      => Str::random(256),
            'team_id'   => $user->team_id,
            'slug'      => $this->generateSlug($user),
            'openai_id' => $request->input('openai_id'),
            'status'    => 'pending',
            'credits'   => 1,
            'storage'   => 'public',
            'payload'   => $payload,
        ];

        return UserOpenai::query()->create($data);
    }

    /**
     * Generate a user-specific slug.
     */
    protected function generateSlug(User $user): string
    {
        return Str::random(7) . Str::slug($user->fullName()) . '-workbook';
    }

    /**
     * Return the appropriate service for the given model.
     */
    protected function getService(EntityEnum $model): BaseService
    {
        return match ($model) {
            EntityEnum::VIDEO_UPSCALER         => app(VideoUpscalerService::class),
            EntityEnum::ANIMATEDIFF_V2V        => app(AnimatediffV2vService::class),
            EntityEnum::FAST_ANIMATEDIFF_TURBO => app(FastAnimatediffTurboService::class),
            EntityEnum::COGVIDEOX_5B           => app(Cogvideox5bService::class),
            // Future models can be added here, for example:
            // EntityEnum::ANIMATEDIFF_V2V => app(AnimatediffV2vService::class),
            default => app(VideoUpscalerService::class),
        };
    }

    /**
     * Get the validation rules based on the request's model.
     */
    protected function validationRules(Request $request): array
    {
        $baseRules = [
            'model'     => 'required|string',
            'video'     => 'required|file',
            'openai_id' => 'required|integer|exists:openai,id',
        ];

        return match ($request->input('model')) {
            EntityEnum::VIDEO_UPSCALER->value => array_merge($baseRules, [
                'scale' => 'required|numeric|min:1|max:8',
            ]),
            EntityEnum::ANIMATEDIFF_V2V->value => array_merge($baseRules, [
                'prompt'                 => 'required|string',
                'negative_prompt'        => 'sometimes',
                'select_every_nth_frame' => 'required|integer|min:1',
            ]),
            EntityEnum::COGVIDEOX_5B->value => array_merge($baseRules, [
                'prompt'              => 'required|string',
                'negative_prompt'     => 'sometimes',
                'num_inference_steps' => 'required|integer|min:1|max:50',
            ]),
            EntityEnum::FAST_ANIMATEDIFF_TURBO->value => array_merge($baseRules, [
                'prompt'          => 'required|string',
                'negative_prompt' => 'sometimes',
                'first_n_seconds' => 'required|numeric|min:1|max:12',
            ]),
            default => $baseRules, // Optionally, an error can be thrown here if model is invalid
        };
    }

    /**
     * Check the status for a specific record.
     */
    public function checked(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:user_openai,id',
        ], [
            'id.exists' => 'There is a problem with the video.',
        ]);

        /** @var UserOpenai $openai */
        $openai = UserOpenai::query()->find($validated['id']);

        return $this->getService($openai->payload['model'])
            ->setOpenai($openai)
            ->checked();
    }
}
