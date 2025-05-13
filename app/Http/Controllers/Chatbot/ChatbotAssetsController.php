<?php

declare(strict_types=1);

namespace App\Http\Controllers\Chatbot;

use App\Support\Chatbot\ChatbotHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ChatbotAssetsController
{
    public function asset(Request $request, string $entryPoint)
    {
        $path = public_path($entryPoint);

        $contentType = ChatbotHelper::getContentTypeFromEntryPoint($entryPoint);

        if (File::exists($path)) {
            $content = File::get($path);

            return response($content, 200)->withHeaders([
                'Content-Type' => $contentType,
                // 'Cache-Control' => 'public, max-age=31536000',
                'Vary'                         => 'Accept-Encoding',
                'Access-Control-Allow-Origin'  => '*',
                'Access-Control-Allow-Methods' => 'GET, HEAD, OPTIONS',
                'Access-Control-Allow-Headers' => 'Accept, Origin, Content-Type, X-MagicAI-Chatbot, X-Livewire',
                // 'Access-Control-Allow-Credentials' => 'false',
            ]);
        }

        abort(404);
    }
}
