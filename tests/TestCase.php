<?php

namespace LivewireAutocomplete\Tests;

use LivewireDuskTestbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public array $packageProviders = [
        \LivewireAutocomplete\LivewireAutocompleteServiceProvider::class,
    ];

    public static function tweakApplicationHook()
    {
        return function () {
            config()->set('livewire-autocomplete.legacy_options.auto-select', false);
            config()->set('livewire-autocomplete.legacy_options.allow-new', false);
        };
    }

    public function viewsDirectory(): string
    {
        return __DIR__.'/views';
    }
}
