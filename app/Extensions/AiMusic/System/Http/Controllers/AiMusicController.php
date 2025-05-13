<?php

namespace App\Extensions\AiMusic\System\Http\Controllers;

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Extensions\AiMusic\System\Models\UserMusic;
use App\Extensions\AiMusic\System\Services\AiMusicService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiMusicController extends Controller
{
    protected AiMusicService $service;

    public function __construct()
    {
        $this->service = new AiMusicService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $list = UserMusic::query()->where('user_id', auth()->user()->id)->get()->toArray();

        $inProgress = collect($list)->filter(function ($entry) {
            return $entry['status'] !== 'complete';
        })->pluck('music_id')->toArray();

        return view('ai-music::index', compact(['list', 'inProgress']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('ai-music::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'audio'   => 'nullable|file|mimes:mp3,wav,ogg|max:10240',
            'link'    => 'nullable|url',
            'purpose' => 'required|in:song,voice,instrumental',
            'lyrics'  => 'required|string',
        ], [
            'audio.required_without' => __('Either an audio file or a link is required.'),
            'link.required_without'  => __('Either an audio file or a link is required.'),
        ]);
        if (! $request->hasFile('audio') && ! $request->input('link')) {
            return back()->withErrors(['audio' => 'Either an audio file or a link must be provided.']);
        }
        if (Helper::appIsDemo()) {
            return redirect()->back()->with([
                'message' => trans('This feature is disabled in demo mode.'), 'type' => 'error',
            ]);
        }
        $driver = Entity::driver(EntityEnum::fromSlug(Setting::getCache()?->ai_music_model))->inputVoiceCount(1)->calculateCredit();

        try {
            $driver->redirectIfNoCreditBalance();
        } catch (Exception $e) {
            return redirect()->back()->with([
                'message' => $e->getMessage(), 'type' => 'error',
            ]);
        }

        $data = $this->service->generateSong($request);

        if ($data['gen_status'] === 'success') {
            UserMusic::query()->create([
                'user_id'   => auth()->user()->id,
                'music_id'  => $data['trace_id'],
                'status'    => 'complete',
                'audio_url' => $data['audio_url'],
                'title'     => 'Unknown',
            ]);

            $driver->decreaseCredit();

            return redirect()->route('dashboard.user.ai-music.index')->with([
                'message' => __('Created Successfully'), 'type' => 'success',
            ]);
        } else {
            return back()->with([
                'message' => $data['message'], 'type' => 'error',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id): JsonResponse|RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        UserMusic::query()->where('music_id', $id)->delete();

        return redirect()->back()->with([
            'message' => __('Deleted Successfully'), 'type' => 'success',
        ]);
    }
}
