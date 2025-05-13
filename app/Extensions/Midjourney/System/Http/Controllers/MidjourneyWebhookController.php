<?php

namespace App\Extensions\Midjourney\System\Http\Controllers;

use App\Extensions\Midjourney\System\Services\PiAPIService;
use App\Http\Controllers\Controller;
use App\Models\UserOpenai;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidjourneyWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Request received',
        ], 200);

        if (! $request->get('data')) {
            return response()->json([
                'error' => 'Request ID not found',
            ], 400);
        }

        $data = $request->get('data');

        $openai = UserOpenai::query()
            ->where('response', 'PI')
            ->where('status', 'IN_QUEUE')
            ->where('request_id', data_get($data, 'task_id'))
            ->first();

        if (! $openai) {
            return response()->json([
                'error' => 'Request not found',
            ], 400);
        }

        $image = data_get($data, 'output.image_url');

        if (empty($image)) {

            $image = PiAPIService::downloadImageToStorage($image);

            $openai->update([
                'output'  => $image ?: $openai->output,
                'payload' => [
                    'size'      => null,
                    'model'     => 'midjourney',
                    'quality'   => 'original',
                ],
                'status'  => 'COMPLETED',
            ]);

            return response()->json([
                'message' => 'Request completed',
            ], 200);
        }

        return response()->json([
            'error' => 'Images not found',
        ], 400);
    }
}
