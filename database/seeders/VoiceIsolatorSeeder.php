<?php

namespace Database\Seeders;

use App\Models\OpenAIGenerator;
use Illuminate\Database\Seeder;

class VoiceIsolatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createVoiceIsolatorRecord();
    }

    public function createVoiceIsolatorRecord(): void
    {
        OpenAIGenerator::query()->firstOrCreate([
            'slug' => 'ai_voice_isolator',
        ], [
            'title'           => 'AI Voice Isolator',
            'description'     => 'Separate voices from background noise in audio recordings.',
            'active'          => 1,
            'questions'       => '[{"name":"file","type":"file","question":"Upload an Audio File (mp3, mp4, mpeg, mpga, m4a, wav, and webm)(Max: 500Mb)","select":""}]',
            'image'           => '<svg xmlns="http://www.w3.org/2000/svg" height="48" viewBox="0 96 960 960" width="48"><path d="M140 976q-24.75 0-42.375-17.625T80 916V236q0-24.75 17.625-42.375T140 176h380l-60 60H140v680h480V776h60v140q0 24.75-17.625 42.375T620 976H140Zm100-170v-60h280v60H240Zm0-120v-60h200v60H240Zm380 10L460 536H320V336h140l160-160v520Zm60-92V258q56 21 88 74t32 104q0 51-35 101t-85 67Zm0 142v-62q70-25 125-90t55-158q0-93-55-158t-125-90v-62q102 27 171 112.5T920 436q0 112-69 197.5T680 746Z"/></svg>',
            'premium'         => 0,
            'type'            => 'isolator',
            'prompt'          => '',
            'custom_template' => '0',
            'tone_of_voice'   => '0',
            'color'           => '#DEFF81',
            'filters'         => 'voice',
        ]);
    }
}
