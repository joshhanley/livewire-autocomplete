<?php

namespace LivewireAutocomplete;

use Illuminate\Support\ServiceProvider;

class LivewireAutocompleteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'lwc');
    }
}
