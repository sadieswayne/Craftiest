<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Chatbot\ChatbotHelper;
use Closure;
use Exception;
use Illuminate\Http\Response;

class CorsMiddleware
{
    use WithChatbotMiddleware;

    public const CHATBOT_EMBED_URI = '/chatbot-api/embed';

    public const CHATBOT_TOKEN_URI = '/chatbot-api/token';

    private array $tokenData;

    public function handle($request, Closure $next)
    {
        $this->setRequest($request);

        if ($this->isChatbotTokenRequest()) {
            return $this->handleChatbotTokenRequest($next);
        }

        if ($this->isChatbotRequest()) {
            return $this->handleChatbotRequest($next);
        }

        if ($this->isChatbotMessageRequest()) {
            return $this->handleChatbotMessageRequest($next);
        }

        return $next($this->request());
    }

    private function handleChatbotTokenRequest($next)
    {
        if (! $this->checkDomain(false)) {
            return response('Unauthorized', 403);
        }

        return $this->responseWithCorsHeaders($next);
    }

    private function handleChatbotRequest($next)
    {

        if (! $this->checkAppKey()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (! $this->checkDomain()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (! $this->checkToken()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $this->loginUser();

        return $this->responseWithCorsHeaders($next);
    }

    private function handleChatbotMessageRequest($next)
    {
        if (! $this->checkAppKey(true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (! $this->checkDomain()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (! $this->checkToken(true)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $this->loginUser();

        return $this->streamedResponseWithCorsHeaders($next);
    }

    private function responseWithCorsHeaders($next)
    {
        /** @var Response $response */
        $response = $next($this->request());

        return $response->withHeaders($this->getCorsHeaders());
    }

    private function streamedResponseWithCorsHeaders($next)
    {
        /** @var Response $response */
        $response = $next($this->request());

        $corsHeaders = $this->getCorsHeaders();

        foreach ($corsHeaders as $key => $value) {
            $response->headers->set($key, $value);
        }

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    private function checkToken(bool $fromInput = false): bool
    {
        if ($fromInput) {
            $this->request()->headers->set('Authorization', 'Bearer ' . $this->request()->input('token'));
        }

        $token = $this->getToken();

        if (! $token) {
            return false;
        }

        try {
            $data = ChatbotHelper::validateJWT($token);

            if (! $data) {
                return false;
            }

            if ($data['sub'] !== $this->getAppKey()) {
                return false;
            }

            if ($data['exp'] < time()) {
                return false;
            }

            if ($data['nbf'] > time()) {
                return false;
            }

            if ($data['aud'] !== $this->getOriginDomain()) {
                return false;
            }

            if ($data['iss'] !== $this->request()->getHost()) {
                return false;
            }

            $this->setTokenData($data);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function setTokenData(array $data): void
    {
        $this->tokenData = $data;
    }

    private function isChatbotTokenRequest(): bool
    {
        $request = $this->request();

        return $request->isMethod('POST') &&
            str()->startsWith($request->getRequestUri(), '/chatbot-api/token');
    }

    private function isChatbotRequest(): bool
    {
        $request = $this->request();

        return $request->isMethod('POST') &&
            str()->startsWith($request->getRequestUri(), '/chatbot-api') &&
            $request->hasHeader('X-MagicAI-Chatbot');
    }

    private function isChatbotMessageRequest(): bool
    {
        $request = $this->request();

        return $request->isMethod('GET') &&
            str()->startsWith($request->getRequestUri(), '/chatbot-api/chatbot-send');
    }
}
