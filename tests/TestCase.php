<?php

namespace LivewireAutocomplete\Tests;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use LivewireDuskTestbench\TestCase as BaseTestCase;
use Orchestra\Testbench\Dusk\Options as DuskOptions;

class TestCase extends BaseTestCase
{
    public array $packageProviders = [
        \LivewireAutocomplete\LivewireAutocompleteServiceProvider::class,
    ];

    public function viewsDirectory(): string
    {
        return __DIR__.'/Browser/views';
    }

    protected function driver(): RemoteWebDriver
    {
        DuskOptions::noSandbox();

        return parent::driver();
    }
}
