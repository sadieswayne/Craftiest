<?php

namespace Database\Seeders;

use App\Models\Extensions\Introduction;
use Illuminate\Database\Seeder;

class IntroductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->createAllIntroduction();
    }

    private function createAllIntroduction(): void
    {
        $introductions = [
            [
                'key'   => 'initialize',
                'intro' => "Welcome to MagicAI. Let's take a quick tour",
                'order' => 1,
            ],
            [
                'key'   => 'ai_writer',
                'intro' => 'A great tool for using the Text Generator & AI Copywriting Assistant.',
                'order' => 2,
            ],
            [
                'key'   => 'ai_image',
                'intro' => 'A great tool for using the Text Generator & AI Copywriting Assistant.',
                'order' => 3,
            ],
            [
                'key'   => 'ai_pdf',
                'intro' => 'Simply upload a PDF, find specific information. extract key insights or summarize the entire document.',
                'order' => 4,
            ],
            [
                'key'   => 'ai_code',
                'intro' => 'Generate high quality code in seconds.',
                'order' => 5,
            ],
            [
                'key'   => 'select_plan',
                'intro' => 'Choose the plan that suits you and start creating right away.',
                'order' => 6,
            ],
            [
                'key'   => 'affiliate_send',
                'intro' => 'Invite your friends and start earning commissions.',
                'order' => 7,
            ],
        ];

        foreach ($introductions as $introduction) {
            $this->createIntro(...$introduction);
        }
    }

    private function createIntro(string $key, string $intro, int $order): void
    {
        Introduction::query()
            ->firstOrCreate([
                'key' => $key,
            ], [
                'key'   => $key,
                'intro' => $intro,
                'order' => $order,
            ]);
    }
}
