<div class="lqd-accordion-item group">
	<button data-target="#{{$id}}" data-trigger-type="accordion" class="flex items-center justify-between w-full py-5 pl-4 text-heading-foreground text-left text-[20px] font-normal tracking-wide border-b group/btn group-last:border-b-0">
		<span>
			{!! $title !!}
		</span>
		<div class="inline-flex items-center justify-center ml-3 border w-7 h-7 rounded-3xl shrink-0">
			<span class="group-[&.lqd-is-active]/btn:hidden">
				<svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					<path d="M6.552 6.489H11.617V5.061H6.552V0H5.082V5.061H5.96046e-08V6.489H5.082V11.571H6.552V6.489Z"/>
				</svg>
			</span>
			<span class="hidden group-[&.lqd-is-active]/btn:block">
				<svg width="7" height="2" viewBox="0 0 7 2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					<path d="M0 1.764C0.633344 1.67982 1.27225 1.64472 1.911 1.659H4.411C5.05675 1.64347 5.70273 1.67858 6.343 1.764V0C5.70266 0.0844217 5.05663 0.11786 4.411 0.0999999H1.911C1.27236 0.117286 0.63335 0.083848 0 0V1.764Z"/>
				</svg>
			</span>
		</div>
	</button>
	<div id={{$id}} class="hidden">
		<div class="py-4 pl-3 lqd-accordion-content">
			<p class="text-lg max-sm:text-[17px]">{!! $content !!}</p>
		</div>
	</div>
</div>