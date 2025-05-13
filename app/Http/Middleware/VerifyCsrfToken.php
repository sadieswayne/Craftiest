<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    use ChatbotCsrf;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // '*',
        'pdf/getContent',
        'stripe/*',
        'webhooks/*',
        'dashboard/*',
        'dashboard/user/payment/iyzico/*',
        'chatbot/*',
        'generator/webhook/fal-ai',
        'dashboard/admin/config/more',
        'translations/lang/update-all',
        'social-media/*',
    ];
}
