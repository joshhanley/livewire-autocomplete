<?php

namespace LivewireAutocomplete\Tests;

use LivewireDuskTestbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public array $packageProviders = [
        \LivewireAutocomplete\ServiceProvider::class,
    ];

    public function viewsDirectory(): string
    {
        return __DIR__.'/views';
    }
}
