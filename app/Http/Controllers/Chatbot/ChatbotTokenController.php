<?php

declare(strict_types=1);

namespace App\Http\Controllers\Chatbot;

use App\Support\Chatbot\ChatbotHelper;
use Illuminate\Http\Request;

class ChatbotTokenController
{
    public function __invoke(Request $request)
    {
        $token = ChatbotHelper::generateJWT($request->input('appKey'));

        if (! $token) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        return response()->json([
            'token' => $token,
        ]);
    }
}
