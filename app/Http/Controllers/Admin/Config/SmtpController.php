<?php

namespace App\Http\Controllers\Admin\Config;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class SmtpController extends Controller
{
    protected $settings;

    public function __construct()
    {
        $this->settings = Setting::query()->first();
    }

    public function index(): View
    {
        return view('panel.admin.config.smtp');
    }

    public function store(Request $request): RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with(['message' => __('This feature is disabled in Demo version.'), 'type' => 'error']);
        }

        $this->settings->update([
            'smtp_host'        => $request->get('smtp_host'),
            'smtp_port'        => $request->get('smtp_port'),
            'smtp_username'    => $request->get('smtp_username'),
            'smtp_password'    => $request->get('smtp_password'),
            'smtp_email'       => $request->get('smtp_email'),
            'smtp_sender_name' => $request->get('smtp_sender_name'),
            'smtp_encryption'  => $request->get('smtp_encryption'),
        ]);

        Setting::forgetCache();

        return back()->with(['message' => 'Updated Successfully.', 'type' => 'success']);
    }

    public function test(Request $request): string|RedirectResponse
    {
        $toEmail = $request->get('test_email');
        $toName = 'Test Email';

        try {
            Mail::raw('Test email content', function ($message) use ($toEmail, $toName) {
                $message->to($toEmail, $toName)
                    ->subject('Test Email');
            });

            return back()->with(['message' => 'Test email sent!', 'type' => 'success']);

        } catch (Exception $exception) {
            return back()->with(['message' => $exception->getMessage(), 'type' => 'error']);
        }
    }
}
