<?php

declare(strict_types=1);

use App\Rules\DomainRule;
use Illuminate\Support\Facades\Validator;

test('passes', function ($domain) {
    $validator = Validator::make(['domain' => $domain], ['domain' => new DomainRule]);
    $this->assertTrue($validator->passes());
})->with([
    'foo.com',
    'foo.com.tr',
    'foo.bar.com',
    'foo.bar.com.tr',
    'www.foo.com',
    'foo123.com',
    'foo-bar.com',
    'foo-bar-123.com',
    'www.foo-bar.com',
    '123.com',
    'www.123.org',
]);

test('failures', function ($domain) {
    $validator = Validator::make(['domain' => $domain], ['domain' => new DomainRule]);
    $this->assertFalse($validator->passes());
})->with([
    'bar',
    '-bar.com',
    '-.-.com',
    'bar.-',
    'foo.com/bar',
    'foo.bar.com/bar.html',
    'foo.a',
    'foo.bar-',
    'https://foo.com',
    'https://foo.com-',
    'https://www.foo.com',
]);
