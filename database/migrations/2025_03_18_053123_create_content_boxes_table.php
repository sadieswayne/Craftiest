<?php

use App\Models\Frontend\ContentBox;
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
        Schema::create('frontend_content_boxes', function (Blueprint $table) {
            $table->id();
            $table->string('emoji')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('background')->nullable();
            $table->string('foreground')->nullable();
            $table->timestamps();
        });

        $items = [
            [
                'emoji'       => '😎',
                'title'       => 'Partner',
                'description' => 'Invite your colleagues and collaborators to join a team and maximize the benefits of AI.',
                'background'  => '#615C5A',
                'foreground'  => '#fff',
            ],
            [
                'emoji'       => '🚀',
                'title'       => 'Collaborate',
                'description' => 'Invite your colleagues and collaborators to join a team and maximize the benefits of AI.',
                'background'  => '#EB6434',
                'foreground'  => '#fff',
            ],
            [
                'emoji'       => '👥',
                'title'       => 'Invite',
                'description' => 'Invite your colleagues and collaborators to join a team and maximize the benefits of AI.',
                'background'  => '#3B4F99',
                'foreground'  => '#fff',
            ],
        ];

        foreach ($items as $item) {
            ContentBox::query()->create($item);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frontend_content_boxes');
    }
};
