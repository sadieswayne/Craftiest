<?php

namespace App\Jobs;

use App\Mail\ConfirmationEmail;
use App\Models\EmailTemplates;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendConfirmationEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $user;

    protected $settings;

    protected $template;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->settings = Setting::getCache();
        $this->template = EmailTemplates::where('id', 1)->first();
    }

    public function handle(): void
    {
        Mail::to($this->user->email)
            ->send(new ConfirmationEmail($this->user, $this->settings, $this->template));
    }

    public function middleware(): array
    {
        return [new WithoutOverlapping($this->user->id)];
    }
}
