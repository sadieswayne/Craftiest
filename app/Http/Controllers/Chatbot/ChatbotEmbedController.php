<?php

declare(strict_types=1);

namespace App\Http\Controllers\Chatbot;

use App\Http\Requests\Chatbot\ChatbotEmbedRequest;
use App\Support\Chatbot\ChatbotHelper;
use Illuminate\Support\Facades\Blade;
use Livewire\Features\SupportScriptsAndAssets\SupportScriptsAndAssets;
use ReflectionException;

class ChatbotEmbedController
{
    /**
     * @throws ReflectionException
     */
    public function __invoke(ChatbotEmbedRequest $request)
    {
        $appKey = $request->input('appKey');

        $componentName = 'chatbot';
        $componentParams = [];

        if (ChatbotHelper::isEmbeddable($componentName) === false) {
            return [$componentName => null];
        }

        $components = collect([
            $componentName => Blade::render('@livewire($component, $params)', [
                'component' => $componentName,
                'params'    => $componentParams,
            ], true),
        ]);

        return [
            'components' => $components,
            'assets'     => SupportScriptsAndAssets::getAssets(),
        ];
    }
}
