<?php

use App\Models\OpenAIGenerator;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $data = '{
					"user_id": null,
					"title": "AI Video to Video",
					"description": "Bring your static images to life and create visually compelling videos effortlessly.",
					"active": 1,
					"questions": "[{\"name\":\"your_description\",\"type\":\"textarea\",\"question\":\"Description\",\"select\":\"\"}]",
					"image": "<svg xmlns=\"http://www.w3.org/2000/svg\" \n width=\"48\" height=\"48\" stroke-width=\"2\" stroke=\"black\" fill=\"none\" viewBox=\"0 0 24 24\"><path stroke=\"none\" d=\"M0 0h24v24H0z\" fill=\"none\"/><path d=\"M6 4l6 16l6 -16\" /></svg>",
					"premium": 0,
					"type": "video-to-video",
					"prompt": null,
					"custom_template": 0,
					"tone_of_voice": 0,
					"color": "#A3D6C2",
					"filters": "video-to-video",
					"package": null
				  }';

        $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        OpenAIGenerator::query()->firstOrCreate([
            'slug' => 'ai_video_to_video',
        ], $data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        OpenAIGenerator::query()->where('slug', 'ai_video_to_video')->delete();
    }
};
