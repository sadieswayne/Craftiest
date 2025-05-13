<?php

	$wrapper_classnames = [ 'lqd-color-box', 'flex', 'items-center', 'py-5', 'px-9', 'gap-4', 'rounded-[15px]', 'transition-all', 'hover:shadow-lg', 'hover:-translate-y-2' ];
	$dot_classnames = [ 'lqd-box-dot', 'w-6', 'h-6', 'border', 'border-[8px]', 'border-white', 'rounded-full', 'shadow-lg' ];

	$color_variants = [
		'orange' => 'text-[#CBA153] bg-[#CBA153] bg-opacity-[0.07] hover:shadow-[#cba15326]',
		'purple' => 'text-[#AB7FE6] bg-[#AB7FE6] bg-opacity-[0.07] hover:shadow-[#ab7fe621]',
		'teal' => 'text-[#57CBC6] bg-[#57CBC6] bg-opacity-[0.07] hover:shadow-[#57cbc624]',
		'blue' => 'text-[#7F8FE6] bg-[#7F8FE6] bg-opacity-[0.07] hover:shadow-[#7f8fe624]',
		'green' => 'text-[#6BAC65] bg-[#6BAC65] bg-opacity-[0.07] hover:shadow-[#6bac6524]',
		'red' => 'text-[#EF793A] bg-[#EF793A] bg-opacity-[0.07] hover:shadow-[#ef793a1f]',
	];

	if ( !empty( $color ) && isset( $color_variants[$color] ) ) {
		array_push( $wrapper_classnames, $color_variants[$color] );
	}

?>
<div class="{{join( ' ', $wrapper_classnames )}}">
	<span class="{{join( ' ', $dot_classnames )}} !bg-current"></span>
	<h3 class="text-xl text-inherit -tracking-tight">{{$title}}</h3>
</div>