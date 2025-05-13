@php
    $statusArr = [
        'not-trained' => [
            'class' => 'default',
            'text' => 'Not Trained',
        ],
        'trained' => [
            'class' => 'success',
            'text' => 'Trained',
        ],
        'training' => [
            'class' => 'info',
            'text' => 'Training',
        ],
    ];
@endphp

<x-badge
    class="text-3xs"
    variant="{{ data_get($statusArr, $status . '.class') ?: 'default' }}"
>
    {{ data_get($statusArr, $status . '.text') ?: 'Waiting' }}
</x-badge>
