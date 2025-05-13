<div
    class="pointer-events-none invisible fixed left-0 right-0 top-[0.5px] z-[999] bg-background opacity-0 transition-opacity"
    id="app-loading-indicator"
    x-data="{}"
    :class="{ 'opacity-0': !$store.appLoadingIndicator.showing, 'invisible': !$store.appLoadingIndicator.showing }"
>
    <div class="lqd-progress relative h-[3px] w-full bg-foreground/10">
        <div class="lqd-progress-bar lqd-progress-bar-indeterminate lqd-app-loading-indicator-progress-bar absolute inset-0 bg-primary dark:bg-heading-foreground">
        </div>
    </div>
</div>
