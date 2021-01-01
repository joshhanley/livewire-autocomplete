<?php

namespace LivewireAutocomplete\Tests\Browser;

use LivewireAutocomplete\LivewireAutocompleteServiceProvider;
use LivewireDusk\TestCase as LivewireDuskTestCase;

class TestCase extends LivewireDuskTestCase
{
    // public $withoutUI = true;

    public $packageProviders = [
        LivewireAutocompleteServiceProvider::class,
    ];
}
