<?php

namespace LivewireAutocomplete\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Features\SupportTesting\BaseTestCase;

class TestCase extends BaseTestCase
{

    protected function getPackageProviders($app)
    {
        return [
            ...parent::getPackageProviders($app),
            \LivewireAutocomplete\ServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        
        $app['config']->set('view.paths', [
            __DIR__ . '/views',
            resource_path('views'),
        ]);
    }
}
