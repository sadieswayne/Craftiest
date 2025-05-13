<?php

namespace App\View\Components;

use App\Domains\Entity\EntityStats;
use App\Models\Plan;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\View\Component;

class CreditList extends Component
{
    public User|Authenticatable|null $user;

    public ?Plan $plan;

    public null|array|SupportCollection $categories;

    public string $showType;

    public string $style;

    public string $legendSize;

    public string $progressHeight;

    public string $labelWords;

    public string $labelImages;

    public string $modalTriggerPos;

    public bool $onlyButton;

    public bool $aiImage;

    public bool $isJs;

    /**
     * Create a new component instance.
     * When setting param type to User Model, it gave error.
     * legend-size="sm/md"
     * style="inline/default"
     * progress-height="sm/md"
     *
     * showType:
     *      'directly' for plans
     *      or 'button' for only button
     *      or 'default' for default
     */
    public function __construct(
        $user = null,
        ?Plan $plan = null,
        string $showType = 'default',
        string $style = 'inline',
        string $legendSize = 'sm',
        string $progressHeight = 'sm',
        string $labelWords = 'Words',
        string $labelImages = 'Images',
        string $modalTriggerPos = 'inline',
        bool $onlyButton = false,
        bool $aiImage = false,
        bool $isJs = true
    ) {
        $this->user = $user ?? auth()->user();
        $this->plan = $plan;
        $this->showType = $showType;
        $this->loadCredits();
        $this->style = $style;
        $this->legendSize = $legendSize;
        $this->progressHeight = $progressHeight;
        $this->labelWords = $labelWords;
        $this->labelImages = $labelImages;
        $this->modalTriggerPos = $modalTriggerPos;
        $this->onlyButton = $onlyButton;
        $this->aiImage = $aiImage;
        $this->isJs = $isJs;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.credit-list', [
            'categories' => $this->categories,
        ]);
    }

    private function loadCredits(): void
    {
        $this->categories = EntityStats::all();
    }
}
