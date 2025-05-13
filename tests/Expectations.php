<?php

declare(strict_types=1);

expect()->extend('seeHtml', function ($values): Pest\Expectation|ExpectationsInterface {
    test()->assertSeeHtml($this->value, $values);

    return $this;
});

expect()->extend('dontSeeHtml', function ($values): Pest\Expectation|ExpectationsInterface {
    test()->assertDontSeeHtml($this->value, $values);

    return $this;
});

expect()->extend('seeHtmlInOrder', function (array $values): Pest\Expectation|ExpectationsInterface {
    test()->assertSeeHtmlInOrder($this->value, $values);

    return $this;
});
