<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\Helper;
use App\Models\Chatbot\Chatbot;
use App\Models\SettingTwo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatBotController extends Controller
{
    public function chatbotIndex()
    {
        $chatbotData = Chatbot::query()->orderBy('id', 'asc')->get();

        return view('panel.chatbot.index', compact('chatbotData'));
    }

    public function addOrUpdate($id = null)
    {
        if ($id == null) {
            $chatbotData = null;
        } else {
            $chatbotData = Chatbot::query()->where('id', $id)->firstOrFail();
        }

        return view('panel.chatbot.form', compact('chatbotData'));
    }

    public function addOrUpdateSave(Request $request)
    {

        if (Helper::appIsNotDemo()) {
            if ($request->template_id != 'undefined') {
                $template = Chatbot::where('id', $request->template_id)->firstOrFail();
            } else {
                $template = new Chatbot;
            }

            $template->title = $request->title;
            $template->role = $request->role;
            $template->model = $request->model;
            $template->first_message = $request->first_message;
            $template->instructions = $request->instructions;
            $template->width = $request->width;
            $template->height = $request->height;

            if ($request->hasFile('image')) {
                $path = 'upload/images/logo/';
                $image = $request->file('image');
                $image_name = Str::random(8) . '-chatbot-img.' . $image->getClientOriginalExtension();

                // Check image file-type
                $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
                if (! in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                    $data = [
                        'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                    ];

                    return response()->json($data, 419);
                }

                $image->move($path, $image_name);

                $template->image = '/' . $path . $image_name;
            }

            $template->save();
        }
    }

    public function delete($id = null)
    {
        if (Helper::appIsNotDemo()) {
            $template = Chatbot::where('id', $id)->firstOrFail();
            $template->delete();

            return back()->with(['message' => __('Deleted Successfully'), 'type' => 'success']);
        }
    }

    public function chatbotSettingsSave(Request $request)
    {
        if (Helper::appIsNotDemo()) {
            $settings_two = SettingTwo::getCache();
            $settings_two->chatbot_status = $request->status;
            $settings_two->chatbot_template = $request->template;
            $settings_two->chatbot_position = $request->position;
            $settings_two->chatbot_login_require = $request->logged_in;
            $settings_two->chatbot_rate_limit = $request->rate_limit;
            $settings_two->chatbot_show_timestamp = $request->chatbot_show_timestamp;
            $settings_two->save();
        } else {
            return response()->json('This feature is disabled in Demo version.', 403);
        }
    }
}
