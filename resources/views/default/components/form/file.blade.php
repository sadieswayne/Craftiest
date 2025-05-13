@aware(['icon', 'action', 'size', 'error'])
@props([
    'icon' => null,
    'action' => null,
    'size' => 'md',
    'error' => null,
])
<x-form.wrapper>
    <x-form.text class="form-control form-file-text" type="file" :attributes="$attributes" />
</x-form.wrapper>