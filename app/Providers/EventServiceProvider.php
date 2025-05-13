<?php

namespace App\Providers;

use App\Events\BankTransferEvent;
use App\Events\FreePaymentEvent;
use App\Events\IyzicoLifetimeEvent;
use App\Events\IyzicoWebhookEvent;
use App\Events\PaypalLifetimeEvent;
use App\Events\PaypalWebhookEvent;
use App\Events\PaystackLifetimeEvent;
use App\Events\PaystackWebhookEvent;
use App\Events\StripeLifetimeEvent;
use App\Events\StripeWebhookEvent;
use App\Events\TwoCheckoutWebhookEvent;
use App\Events\UsersActivityEvent;
use App\Events\YokassaWebhookEvent;
use App\Listeners\BankTransferListener;
use App\Listeners\FreePaymentListener;
use App\Listeners\IyzicoLifetimeListener;
use App\Listeners\IyzicoWebhookListener;
use App\Listeners\PaypalLifetimeListener;
use App\Listeners\PaypalWebhookListener;
use App\Listeners\PaystackLifetimeListener;
use App\Listeners\PaystackWebhookListener;
use App\Listeners\StripeLifetimeListener;
use App\Listeners\StripeWebhookListener;
use App\Listeners\TwoCheckoutWebhookListener;
use App\Listeners\UsersActivityListener;
use App\Listeners\YokassaWebhookListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PaypalWebhookEvent::class => [
            PaypalWebhookListener::class,
        ],
        StripeWebhookEvent::class => [
            StripeWebhookListener::class,
        ],
        YokassaWebhookEvent::class => [
            YokassaWebhookListener::class,
        ],
        TwoCheckoutWebhookEvent::class => [
            TwoCheckoutWebhookListener::class,
        ],
        IyzicoWebhookEvent::class => [
            IyzicoWebhookListener::class,
        ],
        PaystackWebhookEvent::class => [
            PaystackWebhookListener::class,
        ],
        BankTransferEvent::class => [
            BankTransferListener::class,
        ],
        FreePaymentEvent::class => [
            FreePaymentListener::class,
        ],
        StripeLifetimeEvent::class => [
            StripeLifetimeListener::class,
        ],
        PaypalLifetimeEvent::class => [
            PaypalLifetimeListener::class,
        ],
        IyzicoLifetimeEvent::class => [
            IyzicoLifetimeListener::class,
        ],
        PaystackLifetimeEvent::class => [
            PaystackLifetimeListener::class,
        ],
        UsersActivityEvent::class => [
            UsersActivityListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
