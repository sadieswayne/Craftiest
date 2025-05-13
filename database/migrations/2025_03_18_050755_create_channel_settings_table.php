<?php

use App\Models\Frontend\ChannelSetting;
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
        Schema::create('frontend_channel_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('key');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        $data = [
            [
                'key'         => 'facebook',
                'title'       => 'Facebook',
                'description' => "Our AI tool helps you craft compelling content that resonates with your audience Whether it's a status update, promotional post, or a simple interaction.",
                'logo'        => 'assets/landing-page/logo-fb.png',
                'image'       => '/themes/social-media/assets/landing-page/card-fb.jpg',
            ],
            [
                'key'         => 'x',
                'title'       => 'Twitter / X',
                'description' => "Create impactful X posts with our AI tool in seconds! Whether it's a quick update, an engaging tweet, or a conversation starter, generate posts that drive conversations and keep your followers engaged on X.",
                'logo'        => 'assets/landing-page/logo-x.png',
                'image'       => '/themes/social-media/assets/landing-page/card-x.jpg',
            ],
            [
                'key'         => 'instagram',
                'title'       => 'Instagram',
                'description' => "Design eye-catching Instagram posts in no time with our AI-powered generator. From beautiful visuals to captivating captions, create Instagram content that stands out and grabs your followers' attention.",
                'logo'        => 'assets/landing-page/logo-ig.png',
                'image'       => '/themes/social-media/assets/landing-page/card-ig.jpg',
            ],
            [
                'key'         => 'linkedin',
                'title'       => 'LinkedIn',
                'description' => 'Build your professional presence with polished LinkedIn posts. Our AI helps you write insightful articles, thought-provoking updates, and attention-grabbing headlines that engage with your network.',
                'logo'        => 'assets/landing-page/logo-in.png',
                'image'       => '/themes/social-media/assets/landing-page/card-in.jpg',
            ],
        ];

        foreach ($data as $item) {
            ChannelSetting::query()->create($item);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frontend_channel_settings');
    }
};
