<?php

namespace App\Http\Controllers;

use App\Services\Bedrock\BedrockRuntimeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BedrockController extends Controller
{
    protected BedrockRuntimeService $bedrockService;

    public function __construct(BedrockRuntimeService $bedrockService)
    {
        $this->bedrockService = $bedrockService;
    }

    public function generateClaude(Request $request): JsonResponse
    {
        $prompt = $request->get('message');
        $result = $this->bedrockService->invokeClaude($prompt);

        return response()->json($result);
    }

    public function generateImage(Request $request): JsonResponse
    {
        $prompt = $request->input('message');
        $seed = $request->input('seed', random_int(1, 1000000));
        $style_preset = $request->input('style_preset', null);
        $image = $this->bedrockService->invokeStableDiffusion($prompt, $seed, $style_preset);

        return response()->json(['image' => $image]);
    }
}
