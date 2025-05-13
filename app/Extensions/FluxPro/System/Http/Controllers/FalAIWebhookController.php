<?php

namespace App\Extensions\FluxPro\System\Http\Controllers;

use App\Console\Commands\FluxProQueueCheck;
use App\Http\Controllers\Controller;
use App\Models\UserOpenai;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class FalAIWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (! $request->get('request_id')) {
            return response()->json([
                'error' => 'Request ID not found',
            ], 400);
        }

        $openai = UserOpenai::query()
            ->where('response', 'FL')
            ->where('status', 'IN_QUEUE')
            ->where('request_id', $request->get('request_id'))
            ->first();

        if (! $openai) {
            return response()->json([
                'error' => 'Request not found',
            ], 400);
        }

        $payload = $request->get('payload');

        if (! $payload || ! is_array($payload)) {
            return response()->json([
                'error' => 'Payload is wrong',
            ], 400);
        }

        $images = data_get($payload, 'images');

        if (empty($images) || is_array($images)) {
            $firstImage = Arr::first($images);

            $image = data_get($firstImage, 'url');

            $image = FluxProQueueCheck::downloadImageToStorage($image);

            $openai->update([
                'output'  => $image ?: $openai->output,
                'payload' => $payload,
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
