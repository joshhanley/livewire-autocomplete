<?php

namespace LivewireAutocomplete\Tests\Browser;

use Illuminate\Testing\Constraints\SeeInOrder;
use Laravel\Dusk\Browser;
use LivewireAutocomplete\Tests\TestCase as BaseTestCase;
use PHPUnit\Framework\Assert as PHPUnit;

class TestCase extends BaseTestCase
{
    public static function tweakApplicationHook()
    {
        return function () {
            config()->set('autocomplete.options.auto-select', false);
            config()->set('autocomplete.options.allow-new', false);
            config()->set('database.default', 'testbench');
            config()->set('database.connections.testbench', [
                'driver' => 'mysql',
                'database' => 'autocompletetesting',
                'username' => 'root',
                'prefix' => '',
            ]);
        };
    }

    public function setUp(): void
    {
        parent::setUp();

        Browser::macro('assertSeeInOrder', function ($selector, $contents) {
            $fullSelector = $this->resolver->format($selector);

            $element = $this->resolver->findOrFail($selector);

            $contentsString = implode(', ', $contents);

            PHPUnit::assertThat(
                array_map('e', $contents),
                new SeeInOrder($element->getText()),
                "Did not see expected contents [{$contentsString}] within element [{$fullSelector}]."
            );

            return $this;
        });

        $script = '
            let elRect = document.querySelector(`%1$s`).getBoundingClientRect()
            let containerRect = document.querySelector(`%2$s`).getBoundingClientRect()

            return containerRect.top < elRect.bottom && containerRect.bottom > elRect.top
        ';

        Browser::macro('isVisibleInContainer', function ($selector, $container) use ($script) {
            /** @var \Laravel\Dusk\Browser $this */
            $fullSelector = $this->resolver->format($selector);
            $fullContainer = $this->resolver->format($container);

            $this->resolver->findOrFail($selector);
            $this->resolver->findOrFail($container);

            PHPUnit::assertTrue(
                $this->driver->executeScript(sprintf($script, $fullSelector, $fullContainer)),
                "Element [{$fullSelector}] is not visible in [{$fullContainer}]"
            );

            return $this;
        });

        Browser::macro('isNotVisibleInContainer', function ($selector, $container) use ($script) {
            /** @var \Laravel\Dusk\Browser $this */
            $fullSelector = $this->resolver->format($selector);
            $fullContainer = $this->resolver->format($container);

            $this->resolver->findOrFail($selector);
            $this->resolver->findOrFail($container);

            PHPUnit::assertFalse(
                $this->driver->executeScript(sprintf($script, $fullSelector, $fullContainer)),
                "Element [{$fullSelector}] is visible in [{$fullContainer}]"
            );

            return $this;
        });
    }
}
