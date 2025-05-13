<?php

namespace App\Jobs;

use App\Mail\PaymentConfirmationEmail;
use App\Models\EmailTemplates;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPaymentConfirmationEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $user;

    protected $plan;

    protected $settings;

    protected $template;

    public function __construct(User $user, Plan $plan)
    {
        $this->user = $user;
        $this->plan = $plan;
        $this->settings = Setting::getCache();
        $slug = $plan->type === 'subscription' ? 'subscription-successful' : 'payment-successful';
        $this->template = EmailTemplates::where('slug', $slug)->first();
    }

    public function handle(): void
    {
        Mail::to($this->user->email)
            ->send(new PaymentConfirmationEmail($this->user, $this->settings, $this->template, $this->plan));
    }

    public function middleware(): array
    {
        return [new WithoutOverlapping($this->user->id)];
    }
}
