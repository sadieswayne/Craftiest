<?php

namespace App\Jobs;

use App\Mail\InviteTeamEmail;
use App\Models\EmailTemplates;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTeamInviteEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $user;

    protected $sendTo;

    protected $settings;

    protected $template;

    public function __construct(User $user, $sendTo)
    {
        $this->user = $user;
        $this->sendTo = $sendTo;
        $this->settings = Setting::getCache();
        $this->template = EmailTemplates::where('id', 4)->first();
    }

    public function handle()
    {
        Mail::to($this->sendTo)->send(
            new InviteTeamEmail($this->user, $this->settings, $this->template)
        );
    }
}
