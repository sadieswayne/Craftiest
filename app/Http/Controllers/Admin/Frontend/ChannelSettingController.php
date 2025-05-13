<?php

namespace App\Http\Controllers\Admin\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Frontend\ChannelSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChannelSettingController extends Controller
{
    public function index()
    {
        $items = ChannelSetting::query()->get();

        return view('default.panel.admin.frontend.channel-setting.index', compact('items'));
    }

    public function edit(ChannelSetting $channelSetting)
    {
        return view('default.panel.admin.frontend.channel-setting.form', [
            'item' => $channelSetting,
        ]);
    }

    public function update(Request $request, ChannelSetting $channelSetting): RedirectResponse
    {
        $data = $request->validate([
            'title'       => 'required|max:191',
            'description' => 'required|max:2000',
            'image'       => 'sometimes|image|mimes:jpg,jpeg,png',
        ]);

        if ($request->hasFile('image')) {
            $data = '/uploads/' . $request->file('image')->store('frontend', 'uploads');
        }

        $channelSetting->update($data);

        return back()->with([
            'type'    => 'success',
            'message' => __('The Channel Setting was successfully updated'),
        ]);
    }
}
