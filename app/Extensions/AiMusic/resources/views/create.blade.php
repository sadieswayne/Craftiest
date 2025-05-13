@extends('panel.layout.settings')
@section('title', __('AI Music'))
@section('titlebar_subtitle', __('You can generate clip lyrics, a title, and tags based on your short description of the song you want.'))
@section('titlebar_actions', '')
@section('settings')

	<h2 class="mb-4">{{__('Upload or Provide a Link to an Audio File')}}</h2>
	<form id="uploadForm" action="{{ route('dashboard.user.ai-music.store') }}" method="POST" enctype="multipart/form-data">
		@csrf

		<div class="mb-3">
			<label class="form-label">{{__('Upload Audio File')}}</label>
			<input type="file" name="audio" class="form-control">
		</div>

		<div class="mb-3">
			<label class="form-label">{{__('Or Provide an Audio Link')}}</label>
			<input type="url" name="link" class="form-control" placeholder="https://example.com/audio.mp3">
		</div>

		<div class="mb-3">
			<label class="form-label">{{__('Purpose')}}</label>
			<select name="purpose" class="form-control" required>
				<option value="song">{{__('Song')}}</option>
				<option value="voice">{{__('Voice')}}</option>
				<option value="instrumental">{{__('Instrumental')}}</option>
			</select>
		</div>

		<div class="mb-3">
			<div x-data="{ exampleFormat: '' }" class="flex flex-col gap-3">
				<div class="flex justify-between">
					<label> {{ __('Lyrics') }} </label>
					<x-button
					class="chat-completions-fill-btn"
					type="button"
					size="sm"
					@click="exampleFormat =
`[Verse]
Silver cities shine brightly
Skies are painted blue
Hopes and dreams take flight
Future starts anew

[Verse 2]
Machines hum a new tune
Worlds we’ve never seen
Chasing stars so far
Building our own dream

[Chorus]
Future dreams so high
Touch the endless sky
Live beyond the now
Make the future wow

[Bridge]
With every beat we rise
See through wiser eyes
The places we can go
A brilliance that will grow`">
						{{ __('Create example format') }}
					</x-button>
				</div>
				<x-forms.input
					id="lyrics_description_prompt"
					name="lyrics"
					size="lg"
					type="textarea"
					rows="10"
					x-model="exampleFormat"
				>
				</x-forms.input>
			</div>
		</div>
		@if ($app_is_demo)
			<x-button
				type="button"
				onclick="return toastr.info('This feature is disabled in Demo version.');"
			>
				{{ __('Generate Song') }}
			</x-button>
		@else
			<x-button
				type="submit"
				form="uploadForm"
				size="lg"
			>
				{{ __('Generate Song') }}
			</x-button>
		@endif
	</form>

	<div id="responseMessage" class="mt-3"></div>
@endsection
