<x-accordion-item
    id="faq-{{ $item->id }}"
    title="{!! __($item->question) !!}"
    content="{!! __($item->answer) !!}"
/>
