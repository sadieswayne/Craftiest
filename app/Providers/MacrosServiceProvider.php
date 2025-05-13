<?php

namespace App\Providers;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;

class MacrosServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerResponseFactoryMacro();
    }

    public function boot(): void
    {
        //
    }

    private function registerResponseFactoryMacro(): void
    {
        ResponseFactory::macro(
            'success',
            fn (string $message, array $data = []) => $this->json([
                'status'  => 'success',
                'message' => $message,
                'data'    => $data,
            ])
        );

        ResponseFactory::macro(
            'error',
            function (string $message, ?int $statusCode = 422) {
                return $this->json([
                    'status'  => 'error',
                    'message' => $message,
                ], $statusCode);
            });
    }
}
