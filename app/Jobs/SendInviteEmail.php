<?php

namespace App\Jobs;

use App\Mail\InviteEmail;
use App\Models\EmailTemplates;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInviteEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $user;

    protected $sendTo;

    protected $settings;

    protected $template;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, $sendTo)
    {
        $this->user = $user;
        $this->sendTo = $sendTo;
        $this->settings = Setting::getCache();
        $this->template = EmailTemplates::where('id', 2)->first();

    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::to($this->sendTo)->send(new InviteEmail($this->user, $this->settings, $this->template));
    }
}
