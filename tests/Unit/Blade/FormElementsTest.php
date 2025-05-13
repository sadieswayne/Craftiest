<?php

declare(strict_types=1);

test('text', function () {

    $expectedValue = <<<'HTML'
<input class="form-control" name="foo"
           :id="$id('text-input')"
           type="text"
           
    />
HTML;

    expect($this->blade('<x-form.text name="foo" />'))->seeHtml($expectedValue);
});

test('text for livewire', function () {

    $expectedValue = <<<'HTML'
<input class="form-control" wire:model="foo.bar"
           :id="$id('text-input')"
           type="text"
           
    />
HTML;

    expect($this->blade($expectedValue))->seeHtml($expectedValue);
});
