<?php

namespace LivewireAutocomplete\Tests;

use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    public function boot()
    {
        config()->set('autocomplete.options.auto_select', false);
        config()->set('autocomplete.options.allow_new', false);
    }
}
