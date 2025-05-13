<?php

namespace App\Http\Controllers;

use App\Actions\Notify;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('panel.admin.announcements.index');
    }

    public function store(Request $request)
    {
        if ($request->input('announcement_active')) {
            $request->validate([
                'announcement_title'       => 'required|string|max:255',
                'announcement_description' => 'required|string',

                'announcement_image'      => 'nullable',
                'announcement_image_dark' => 'nullable',

                'announcement_background_color'      => 'nullable',
                'announcement_background_color_dark' => 'nullable',

                'announcement_background_image'      => 'nullable',
                'announcement_background_image_dark' => 'nullable',
            ]);
            $title = $request->input('announcement_title');
            $message = $request->input('announcement_description');
            $url = $request->input('announcement_url', '#');
            $button_text = $request->input('announcement_button_text', 'Read More');
            $background_color = $request->input('announcement_background_color', '#ffffff');
            $background_color_dark = $request->input('announcement_background_color_dark', '#1a1d23');

            $image_name = setting('announcement_image', '/upload/images/speaker.png');
            if ($request->hasFile('announcement_image')) {
                $path = 'upload/images/';
                $image = $request->file('announcement_image');
                $image_name = Str::random(8) . '-ann-img.' . $image->getClientOriginalExtension();
                $image->move($path, $image_name);
                $image_name = '/' . $path . $image_name;
            }

            $dark_image_name = setting('announcement_image_dark', '/upload/images/speaker.png');
            if ($request->hasFile('announcement_image_dark')) {
                $path = 'upload/images/';
                $image = $request->file('announcement_image_dark');
                $dark_image_name = Str::random(8) . '-ann-img-dark.' . $image->getClientOriginalExtension();
                $image->move($path, $dark_image_name);
                $dark_image_name = '/' . $path . $dark_image_name;
            }

            $dark_bg_image_name = setting('announcement_background_image_dark', null);
            if ($request->hasFile('announcement_background_image_dark')) {
                $path = 'upload/images/';
                $image = $request->file('announcement_background_image_dark');
                $dark_bg_image_name = Str::random(8) . '-ann-bg-dark.' . $image->getClientOriginalExtension();
                $image->move($path, $dark_bg_image_name);
                $dark_bg_image_name = '/' . $path . $dark_bg_image_name;
            }

            $bg_image_name = setting('announcement_background_image', null);
            if ($request->hasFile('announcement_background_image')) {
                $path = 'upload/images/';
                $image = $request->file('announcement_background_image');
                $bg_image_name = Str::random(8) . '-ann-bg.' . $image->getClientOriginalExtension();
                $image->move($path, $bg_image_name);
                $bg_image_name = '/' . $path . $bg_image_name;
            }

            setting(
                [
                    'announcement_active'                => true,
                    'announcement_title'                 => $title,
                    'announcement_description'           => $message,
                    'announcement_url'                   => $url,
                    'announcement_button_text'           => $button_text,
                    'announcement_image'                 => $image_name,
                    'announcement_image_dark'            => $dark_image_name,
                    'announcement_background_color'      => $background_color,
                    'announcement_background_color_dark' => $background_color_dark,
                    'announcement_background_image'      => $bg_image_name,
                    'announcement_background_image_dark' => $dark_bg_image_name,
                ]
            )->save();

            return redirect()->route('dashboard.admin.announcements.index')->with('success', __('Notification sent to all users.'));
        }

        $request->validate([
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
            'url'     => 'nullable|url',
        ]);
        $title = $request->input('title');
        $message = $request->input('message');
        $url = $request->input('url', '#');
        $users = User::all();
        Notify::toMany($users, $title, $message, $url);

        return redirect()->route('dashboard.admin.announcements.index')->with('success', __('Notification sent to all users.'));
    }

    // notify
    public function re_notify(Request $request)
    {
        User::query()->update(['dash_notify_seen' => false]);

        return response()->json(['message' => __('All users will be notified again.'), 'success' => true]);
    }

    // reset
    public function reset(Request $request)
    {
        setting([
            'announcement_active'                => 0,
            'announcement_title'                 => null,
            'announcement_description'           => null,
            'announcement_url'                   => null,
            'announcement_button_text'           => null,
            'announcement_image'                 => null,
            'announcement_image_dark'            => null,
            'announcement_background_color'      => null,
            'announcement_background_color_dark' => null,
            'announcement_background_image'      => null,
            'announcement_background_image_dark' => null,
        ])->save();

        return response()->json(['message' => __('Announcement has been reset.'), 'success' => true]);
    }
}
