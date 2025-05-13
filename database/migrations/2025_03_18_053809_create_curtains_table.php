<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('frontend_curtains', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('title_icon')->nullable();
            $table->json('sliders')->nullable();
            $table->timestamps();
        });

        $items = [
            [
                'title'       => 'Sarah J.',
                'title_icon'  => '',
                'sliders'     => [
                    [
                        'title'             => 'Sarah J.',
                        'description'       => 'Translates Podcasts into different languages.',
                        'bg_color'          => '',
                        'bg_image'          => '',
                        'bg_video'          => '/themes/social-media/assets/landing-page/demo-vid-1.webm',
                        'title_color'       => '',
                        'description_color' => '',
                    ],
                    [
                        'title'             => 'Sarah J.',
                        'description'       => '',
                        'bg_color'          => '',
                        'bg_image'          => '',
                        'bg_video'          => '/themes/social-media/assets/landing-page/demo-vid-1.webm',
                        'title_color'       => '',
                        'description_color' => '',
                    ],
                    [
                        'title'             => 'Sarah J.',
                        'description'       => '',
                        'bg_color'          => '',
                        'bg_image'          => '',
                        'bg_video'          => '/themes/social-media/assets/landing-page/demo-vid-1.webm',
                        'title_color'       => '',
                        'description_color' => '',
                    ],
                ],
            ],
            [
                'title'       => 'Jason R.',
                'title_icon'  => '',
                'sliders'     => [
                    [
                        'title'             => 'Jason R.',
                        'description'       => '',
                        'bg_color'          => '#aea397',
                        'bg_image'          => '',
                        'bg_video'          => '/themes/social-media/assets/landing-page/demo-vid-1.webm',
                        'title_color'       => '',
                        'description_color' => '',
                    ],
                    [
                        'title'             => 'Jason R.',
                        'description'       => '',
                        'bg_color'          => '#aea397',
                        'bg_image'          => '/themes/social-media/assets/landing-page/banner-img.jpg',
                        'bg_video'          => '',
                        'title_color'       => '',
                        'description_color' => '',
                    ],
                ],
            ],
            [
                'title'       => 'Mary J.',
                'title_icon'  => '',
                'sliders'     => [
                    [
                        'title'             => 'Mary J.',
                        'description'       => '',
                        'bg_color'          => '#496e8f',
                        'bg_image'          => '',
                        'bg_video'          => '/themes/social-media/assets/landing-page/demo-vid-1.webm',
                        'title_color'       => '',
                        'description_color' => '',
                    ],
                ],
            ],
        ];

        foreach ($items as $item) {
            \App\Models\Frontend\Curtain::create($item);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frontend_curtains');
    }
};
