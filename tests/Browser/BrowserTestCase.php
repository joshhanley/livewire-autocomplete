<?php

namespace LivewireAutocomplete\Tests\Browser;

use function Livewire\trigger;

use Illuminate\Testing\Constraints\SeeInOrder;
use Laravel\Dusk\Browser;
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
    }

    public function tearDown(): void
    {
        trigger('browser.testCase.tearDown', $this);

        parent::tearDown();
    }
}
