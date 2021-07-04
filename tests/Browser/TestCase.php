<?php

namespace LivewireAutocomplete\Tests\Browser;

use LivewireAutocomplete\LivewireAutocompleteServiceProvider;
use LivewireAutocomplete\Tests\TestServiceProvider;
use LivewireDuskTestbench\TestCase as LivewireDuskTestbenchTestCase;

class TestCase extends LivewireDuskTestbenchTestCase
{
    // public $withoutUI = true;

    public $packageProviders = [
        LivewireAutocompleteServiceProvider::class,
        TestServiceProvider::class,
    ];

    public function configureViewsDirectory()
    {
        $this->viewsDirectory = __DIR__ . '/views';
    }
}
