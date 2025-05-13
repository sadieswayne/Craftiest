<?php

use App\Models\OpenAIGenerator;
use App\Models\OpenaiGeneratorChatCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('user_openai_chat', 'website_url')) {
            OpenAIGenerator::query()
                ->firstOrCreate([
                    'slug' => 'ai_webchat',
                ], [
                    'title'           => 'AI Web Chat',
                    'description'     => 'Analyze web page content with url',
                    'active'          => 1,
                    'questions'       => '[{\"name\":\"your_description\",\"type\":\"textarea\",\"question\":\"Description\",\"select\":\"\"}]',
                    'image'           => '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" stroke-width="2" stroke="black" fill="none" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4l6 16l6 -16" /></svg>',
                    'premium'         => 0,
                    'type'            => 'text',
                    'prompt'          => null,
                    'custom_template' => 0,
                    'tone_of_voice'   => 0,
                    'color'           => '#A3D6C2',
                    'filters'         => 'blog',
                ]);

            OpenaiGeneratorChatCategory::query()
                ->firstOrCreate([
                    'slug' => 'ai_webchat',
                ], [
                    'name'             => 'WebChat',
                    'short_name'       => 'WC',
                    'description'      => 'AI Web Chat',
                    'role'             => 'Web Analyzer',
                    'human_name'       => 'AI Web Chat',
                    'helps_with'       => 'I can assist you with web page content analyzation',
                    'prompt_prefix'    => 'As a WebPage analyzer',
                    'image'            => 'assets/img/webchat.png',
                    'color'            => '#EDBBBE',
                    'chat_completions' => '[{"role": "system", "content": "You are a Web Page Analyzer assistant."}]',
                    'plan'             => '',
                    'category'         => '',
                ]);
        }
    }

    public function down(): void {}
};
