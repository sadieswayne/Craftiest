<?php

namespace App\Jobs;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $user;

    protected $sendTo;

    protected $settings;

    protected $template;

    public function __construct(array $user, $sendTo, $template)
    {
        $this->user = $user;
        $this->sendTo = $sendTo;
        $this->settings = Setting::getCache();
        $this->template = $template;
    }

    public function handle()
    {
        Mail::to($this->sendTo)->send(
            new \App\Mail\SendEmail($this->user, $this->settings, $this->template)
        );

        if (config('queue.default') == 'sync') {
            sleep(rand(1, 4));
        }
    }
}
