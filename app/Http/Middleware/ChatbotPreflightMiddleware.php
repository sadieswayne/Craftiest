<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ChatbotPreflightMiddleware
{
    use WithChatbotMiddleware;

    public function handle($request, Closure $next)
    {
        $this->setRequest($request);

        if ($this->isChatbotPreflightRequest($request)) {
            return $this->handleChatbotPreflightRequest($next);
        }

        return $next($this->request());
    }

    private function isChatbotPreflightRequest(Request $request): bool
    {
        if (! $request->isMethod('OPTIONS')) {
            return false;
        }

        return str()->startsWith($request->getRequestUri(), '/chatbot-api');
    }

    private function handleChatbotPreflightRequest($next)
    {
        if (! $this->checkDomain(false)) {
            return response('Unauthorized', 403);
        }

        return response('', 200)->withHeaders($this->getCorsHeaders());
    }
}
