<?php

namespace App\Http\Controllers\Voice;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Jobs\Voice\ElevenlabVoiceCreateJob;
use App\Models\Voice\ElevenlabVoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ElevenlabVoiceController extends Controller
{
    public function index()
    {
        $items = ElevenlabVoice::query()
            ->where('user_id', Auth::id())
            ->paginate();

        return view('panel.admin.voice.index', compact('items'));
    }

    public function create()
    {
        return view('panel.admin.voice.edit', [
            'item'   => new ElevenlabVoice,
            'title'  => __('Add Voice'),
            'action' => route('dashboard.user.voice.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $data = $request->validate([
            'name'   => 'required',
            'status' => 'required',
            'file'   => 'required|file|mimes:mp3,wav,ogg,flac,webm,mp4',
        ]);

        if ($request->hasFile('file')) {
            $data['path'] = $request->file('file')->store('voices', ['disk' => 'local']);
        }

        $data['user_id'] = Auth::id();

        $elevenlabVoice = ElevenlabVoice::query()->create($data);

        ElevenlabVoiceCreateJob::dispatch($elevenlabVoice->getAttribute('id'));

        return to_route('dashboard.user.voice.index')->with([
            'type'    => 'success',
            'message' => __('Voice added successfully.'),
        ]);
    }

    public function edit(ElevenlabVoice $voice)
    {
        return view('panel.admin.voice.edit', [
            'item'   => $voice,
            'title'  => __('Edit Voice'),
            'action' => route('dashboard.user.voice.update', $voice),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, ElevenlabVoice $voice)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $data = $request->validate([
            'name'   => 'required',
            'status' => 'required',
        ]);

        $voice->update($data);

        return to_route('dashboard.user.voice.index')->with([
            'type'    => 'success',
            'message' => __('Voice updated successfully.'),
        ]);
    }

    public function delete(ElevenlabVoice $voice)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $voice->delete();

        return back()->with([
            'type'    => 'success', // success, error, warning, info,
            'message' => trans('Voice deleted successfully.'),
        ]);
    }
}
