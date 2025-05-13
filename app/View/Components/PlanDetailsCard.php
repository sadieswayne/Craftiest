<?php

namespace App\View\Components;

use App\Models\OpenAIGenerator;
use App\Models\Plan;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class PlanDetailsCard extends Component
{
    public OpenAIGenerator|Collection|null $allFeatures;

    public ?Plan $plan;

    public string $period;

    public string $style;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?Plan $plan = null,
        string $period = 'month',
        string $style = 'style-1'
    ) {
        $this->assignFeatures();
        $this->plan = $plan;
        $this->period = $period;
        $this->style = $style;
    }

    private function assignFeatures(): void
    {
        $this->allFeatures = OpenAIGenerator::getCache(static function () {
            return OpenAIGenerator::query()->get();
        })->groupBy('filters');

        $this->appendSlugMissingFeatures();
    }

    private function appendSlugMissingFeatures(): void
    {
        $missingFeatures = [
            [
                'slug'    => 'ai_chat_all',
                'title'   => __('AI Chat'),
                'filters' => 'blog',
            ],
            [
                'slug'    => 'ai_voiceover_clone',
                'title'   => __('Voiceover Clone'),
                'filters' => 'voice',
            ],
            [
                'slug'    => 'ai_plagiarism_extension',
                'title'   => __('Plagiarism Checker'),
                'filters' => 'blog',
            ],
            [
                'slug'    => 'brand_voice',
                'title'   => __('Brand Voice'),
                'filters' => 'branding',
            ],
        ];

        foreach ($missingFeatures as $feature) {
            $item = new OpenAIGenerator;
            $item->slug = $feature['slug'];
            $item->title = $feature['title'];
            $item->filters = $feature['filters'];

            if (! $this->allFeatures->has($item->filters)) {
                $this->allFeatures[$item->filters] = collect();
            }
            $this->allFeatures[$item->filters]->push($item);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.plan-details-card');
    }
}
