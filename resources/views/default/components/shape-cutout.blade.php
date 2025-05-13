@props([
    'elId' => uniqid(''),
    'position' => 'br', // br, bl, tr, tl
    'width' => '680px',
    'height' => '180px',
    'roundness' => '12px',
    'x' => '0px',
    'y' => '0px',
    'skew' => '0deg',
    'extendedCorner' => [],
])

@php
    $base_classname = "lqd-cutout cutout-{$elId} lqd-cutout-1 lqd-cutout-{$position} pointer-events-none hidden lg:block";
@endphp

@push('css')
    <style>
        @media (min-width: 992px) {
            #{{ $elId }} {
                mask-image: linear-gradient(0deg, black, black), url({{ '#' . 'lqd-cutout-' . $elId }});
                mask-composite: exclude;
            }

            .cutout-{{ $elId }} {
                --shape-w: {{ $width }};
                --shape-h: {{ $height }};
                --shape-roundness: {{ $roundness }};
                --shape-x: {{ $x }};
                --shape-y: {{ $y }};
                --shape-skew: {{ $skew }};
                --extended-corner-r: {{ $extendedCorner['r'] ?? '50px' }};
            }
        }
    </style>
@endpush

<div
    id="cutout-{{ $elId }}"
    {{ $attributes->withoutTwMergeClasses()->twMerge($base_classname) }}
>
    <div x-data="shapeCutout">
        <svg
            class="lqd-cutout-svg pointer-events-none absolute start-0 top-0 -z-1 size-full"
            xmlns="http://www.w3.org/2000/svg"
            role="none"
            width="100"
            height="100"
            fill="none"
        >
            <mask id="lqd-cutout-mask-circle-bs-{{ $elId }}">
                <circle
                    class="lqd-cutout-mask-circle lqd-cutout-mask-circle-bs lqd-cutout-mask-circle-bs-mask-fill"
                    cx="50"
                    cy="50"
                    r="50"
                    fill="white"
                ></circle>
                <circle
                    class="lqd-cutout-mask-circle lqd-cutout-mask-circle-bs lqd-cutout-mask-circle-bs-mask-clip"
                    cx="50"
                    cy="50"
                    r="50"
                    fill="black"
                ></circle>
            </mask>

            <mask id="lqd-cutout-mask-circle-{{ (int) $x === 0 ? 'te' : 'be' }}-{{ $elId }}">
                <circle
                    class="lqd-cutout-mask-circle lqd-cutout-mask-circle-{{ (int) $x === 0 ? 'te' : 'be' }} lqd-cutout-mask-circle-{{ (int) $x === 0 ? 'te' : 'be' }}-mask-fill"
                    cx="50"
                    cy="50"
                    r="50"
                    fill="white"
                ></circle>
                <circle
                    class="lqd-cutout-mask-circle lqd-cutout-mask-circle-{{ (int) $x === 0 ? 'te' : 'be' }} lqd-cutout-mask-circle-{{ (int) $x === 0 ? 'te' : 'be' }}-mask-clip"
                    cx="50"
                    cy="50"
                    r="50"
                    fill="black"
                ></circle>
            </mask>

            @if (filled($extendedCorner))
                <mask id="lqd-cutout-mask-extended-corner-bs-{{ $elId }}">
                    <circle
                        class="lqd-cutout-mask-extended-corner-mask-bs lqd-cutout-mask-extended-corner-bs-mask-fill"
                        cx="50"
                        cy="50"
                        r="50"
                        fill="white"
                    ></circle>
                    <circle
                        class="lqd-cutout-mask-extended-corner-mask-bs lqd-cutout-mask-extended-corner-bs-mask-clip"
                        cx="50"
                        cy="50"
                        r="50"
                        fill="black"
                    ></circle>
                </mask>
                <mask id="lqd-cutout-mask-extended-corner-be-{{ $elId }}">
                    <circle
                        class="lqd-cutout-mask-extended-corner-mask-be lqd-cutout-mask-extended-corner-be-mask-fill"
                        cx="50"
                        cy="50"
                        r="50"
                        fill="white"
                    ></circle>
                    <circle
                        class="lqd-cutout-mask-extended-corner-mask-be lqd-cutout-mask-extended-corner-be-mask-clip"
                        cx="50"
                        cy="50"
                        r="50"
                        fill="black"
                    ></circle>
                </mask>
            @endif

            <defs>
                <mask id="lqd-cutout-{{ $elId }}">
                    <g class="lqd-cutout-mask-g-wrap">
                        <g class="lqd-cutout-mask-g">
                            <rect
                                class="lqd-cutout-mask-rect lqd-cutout-mask-rect-1"
                                width="760"
                                height="160"
                                rx="38"
                                fill="white"
                            ></rect>
                            @if ((int) $x === 0)
                                <rect
                                    class="lqd-cutout-mask-rect lqd-cutout-mask-rect-fill"
                                    width="760"
                                    height="160"
                                    fill="white"
                                ></rect>
                            @endif
                            <circle
                                class="lqd-cutout-mask-circle lqd-cutout-mask-circle-bs"
                                cx="50"
                                cy="50"
                                r="50"
                                fill="white"
                                mask="url(#lqd-cutout-mask-circle-bs-{{ $elId }})"
                            ></circle>
                            <circle
                                @class([
                                    'lqd-cutout-mask-circle',
                                    'lqd-cutout-mask-circle-te' => (int) $x === 0,
                                    'lqd-cutout-mask-circle-be' => (int) $x !== 0,
                                ])
                                cx="50"
                                cy="50"
                                r="50"
                                fill="white"
                                mask="url(#lqd-cutout-mask-circle-{{ (int) $x === 0 ? 'te' : 'be' }}-{{ $elId }})"
                            ></circle>

                            @if (filled($extendedCorner))
                                <circle
                                    class='lqd-cutout-mask-extended-corner'
                                    cx="50"
                                    cy="50"
                                    r="50"
                                    fill="white"
                                ></circle>
                                <circle
                                    class='lqd-cutout-mask-extended-corner-mask-bs'
                                    cx="50"
                                    cy="50"
                                    r="50"
                                    fill="white"
                                    mask="url(#lqd-cutout-mask-extended-corner-bs-{{ $elId }})"
                                ></circle>
                                <circle
                                    class='lqd-cutout-mask-extended-corner-mask-be'
                                    cx="50"
                                    cy="50"
                                    r="50"
                                    fill="white"
                                    mask="url(#lqd-cutout-mask-extended-corner-be-{{ $elId }})"
                                ></circle>
                            @endif
                        </g>
                    </g>
                </mask>
            </defs>
        </svg>
    </div>
</div>
