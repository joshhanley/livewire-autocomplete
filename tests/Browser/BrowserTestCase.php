<?php

namespace LivewireAutocomplete\Tests\Browser;

use function Livewire\trigger;
use Illuminate\Testing\Constraints\SeeInOrder;
use Laravel\Dusk\Browser;
use Livewire\Features\SupportTesting\BaseBrowserTestCase;

use Livewire\Features\SupportTesting\BaseTestCase;
use LivewireAutocomplete\Tests\TestCase;
use PHPUnit\Framework\Assert as PHPUnit;

class BrowserTestCase extends TestCase
{
    public static function tweakApplicationHook()
    {
        return function () {};
    }

    public function setUp(): void
    {
        parent::setUp();

        trigger('browser.testCase.setUp', $this);

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

    public function tearDown(): void
    {
        trigger('browser.testCase.tearDown', $this);

        parent::tearDown();
    }
}
