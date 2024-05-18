<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\DynamicComponentsTest;

use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class DynamicComponentsTest extends TestCase
{
    /** @test */
    public function it_shows_custom_component_when_passed_into_the_instance_through_props()
    {
        Livewire::visit(DynamicResultRowComponent::class)
            ->click('@autocomplete-input')
            // Pause to allow transitions to run
            ->pause(100)
            ->assertSeeIn('@result-0', 'Custom Row');
    }
}
