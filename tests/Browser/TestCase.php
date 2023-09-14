<?php

namespace LivewireAutocomplete\Tests\Browser;

use Livewire\Features\SupportTesting\BaseTestCase;
use LivewireAutocomplete\LivewireAutocompleteServiceProvider;
use LivewireAutocomplete\Tests\TestServiceProvider;
use LivewireDuskTestbench\TestCase as LivewireDuskTestbenchTestCase;

use function Livewire\trigger;

class TestCase extends BaseTestCase
{
    // public $withoutUI = false;

    // public $packageProviders = [
    //     LivewireAutocompleteServiceProvider::class,
    //     TestServiceProvider::class,
    // ];

    // public function configureViewsDirectory()
    // {
    //     $this->viewsDirectory = __DIR__ . '/views';
    // }

    protected function getPackageProviders($app)
    {
        return [
            ...parent::getPackageProviders($app),
            LivewireAutocompleteServiceProvider::class,
            TestServiceProvider::class
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

    public static function tweakApplicationHook()
    {
        return function () {};
    }

    public function setUp(): void
    {
        parent::setUp();

        trigger('browser.testCase.setUp', $this);
    }

    public function tearDown(): void
    {
        trigger('browser.testCase.tearDown', $this);

        parent::tearDown();
    }
}
