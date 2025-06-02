<?php

namespace LivewireAutocomplete\Tests;

use LivewireDuskTestbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public array $packageProviders = [
        \LivewireAutocomplete\LivewireAutocompleteServiceProvider::class,
    ];

    public function viewsDirectory(): string
    {
        return __DIR__.'/Browser/views';
    }
}
