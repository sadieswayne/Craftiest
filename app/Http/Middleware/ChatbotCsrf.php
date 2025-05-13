<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Chatbot\ChatbotHelper;
use Closure;
use Illuminate\Http\Request;
use JsonException;
use Livewire\LivewireManager;
use ReflectionException;

trait ChatbotCsrf
{
    /**
     * @param  \Illuminate\Http\Request  $request
     *
     * @throws \Illuminate\Session\TokenMismatchException
     * @throws ReflectionException|JsonException
     */
    public function handle($request, Closure $next): mixed
    {
        if ($this->isLivewireUpdateRequest($request)) {
            foreach ($request->json('components', []) as $component) {
                $componentName = $this->getComponentNameFromSnapshot($component);

                if ($componentName && ! ChatbotHelper::isEmbeddable($componentName)) {
                    return parent::handle($request, $next);
                }
            }
        }

        return $next($request);
    }

    private function isLivewireUpdateRequest(Request $request): bool
    {
        return $request->isMethod('POST') &&
            app(LivewireManager::class)->getUpdateUri() === $request->getRequestUri() &&
            $request->hasHeader('X-MagicAI-Chatbot') &&
            $request->hasHeader('X-Livewire');
    }

    /**
     * @throws JsonException
     */
    private function getComponentNameFromSnapshot(array $component): ?string
    {
        $snapshot = json_decode($component['snapshot'], true, 512, JSON_THROW_ON_ERROR);

        return $snapshot['memo']['name'] ?? null;
    }
}
