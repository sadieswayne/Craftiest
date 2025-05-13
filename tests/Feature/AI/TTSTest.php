<?php

declare(strict_types=1);

namespace Tests\Feature\AI\TTS;

use App\Http\Controllers\TTSController;
use App\Models\Setting;
use App\Models\SettingTwo;
use Illuminate\Http\Request;

function mockSettings(): void
{
    Setting::factory()->create(['gcs_file' => 'fake-gcs.json']);
    SettingTwo::factory()->create(['daily_voice_limit_enabled' => false]);
}

beforeEach(function () {
    $this->seed();
});

test('returns error if no speeches are provided', function () {
    mockSettings();

    $controller = new TTSController;
    $request = Request::create('/generate-speech', 'POST', [
        'speeches' => json_encode([], JSON_THROW_ON_ERROR),
    ]);

    $response = $controller->generateSpeech($request);
    expect($response->getStatusCode())->toBe(429)
        ->and($response->getData()->errors[0])->toBe('Please provide inputs.');
});
