<?php

use Scolmore\ZeroTrust\Tests\TestCase;

uses(TestCase::class)->in('Unit');

test('die and dump is not used')
    ->expect('dd')
    ->not
    ->toBeUsed();

test('dump is not used')
    ->expect('dump')
    ->not
    ->toBeUsed();

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
