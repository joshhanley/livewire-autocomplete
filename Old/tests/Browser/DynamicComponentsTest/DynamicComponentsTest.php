<?php

namespace LivewireAutocomplete\Tests\Browser\DynamicComponentsTest;

use Laravel\Dusk\Browser;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\TestCase;

class DynamicComponentsTest extends TestCase
{
    /** @test */
    public function it_shows_custom_component_when_passed_into_the_instance_through_props()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, DynamicResultRowComponent::class)
                ->click('@autocomplete-input')
                // Pause to allow transitions to run
                ->pause(100)
                ->assertSeeIn('@result-0', 'Custom Row')
                ;
        });
    }
}
