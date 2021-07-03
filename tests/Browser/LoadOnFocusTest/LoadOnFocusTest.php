<?php

namespace LivewireAutocomplete\Tests\Browser\LoadOnFocusTest;

use Laravel\Dusk\Browser;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\TestCase;

class LoadOnFocusTest extends TestCase
{
    /** @test */
    public function results_are_not_loaded_initially()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, LoadOnFocusComponent::class)
                ->assertSeeIn('@number-times-calculate-called', 0)
                ->assertDontSeeIn('@autocomplete-dropdown', 'bob')
                ->assertDontSeeIn('@autocomplete-dropdown', 'john')
                ->assertDontSeeIn('@autocomplete-dropdown', 'bill')
                ;
        });
    }

    /** @test */
    public function it_loads_results_on_focus_if_action_is_present()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, LoadOnFocusComponent::class)
                ->waitForLivewire()->click('@autocomplete-input')
                ->assertSeeIn('@number-times-calculate-called', 1)
                ->assertSeeIn('@autocomplete-dropdown', 'bob')
                ->assertSeeIn('@autocomplete-dropdown', 'john')
                ->assertSeeIn('@autocomplete-dropdown', 'bill')
                ;
        });
    }

    /** @test */
    public function it_only_loads_results_once_if_load_once_on_focus_is_set_to_true()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, LoadOnFocusComponent::class)
                ->waitForLivewire()->click('@autocomplete-input')
                ->assertSeeIn('@number-times-calculate-called', 1)
                ->keys('@autocomplete-input', '{ESCAPE}')
                ->click('@autocomplete-input')
                // Wait for livewire request if it was going to happen (it shouldn't)
                ->pause(100)
                ->assertSeeIn('@number-times-calculate-called', 1)
                ;
        });
    }

    /** @test */
    public function it_loads_results_on_every_focus_if_load_once_on_focus_is_set_to_true()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, LoadOnFocusComponent::class, '?loadOnceOnFocus=false')
                ->waitForLivewire()->click('@autocomplete-input')
                ->assertSeeIn('@number-times-calculate-called', 1)
                ->keys('@autocomplete-input', '{ESCAPE}')
                ->waitForLivewire()->click('@autocomplete-input')
                ->assertSeeIn('@number-times-calculate-called', 2)
                ;
        });
    }

    /** @test */
    public function it_does_not_load_on_focus_if_there_is_a_value_in_the_input()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, LoadOnFocusComponent::class, '?loadOnceOnFocus=false')
                ->waitForLivewire()->click('@autocomplete-input')
                ->assertSeeIn('@number-times-calculate-called', 1)
                ->waitForLivewire()->type('@autocomplete-input', 'b')
                ->keys('@autocomplete-input', '{ESCAPE}')
                ->click('@autocomplete-input')
                // Wait for livewire request if it was going to happen (it shouldn't)
                ->pause(100)
                ->assertSeeIn('@number-times-calculate-called', 1)
                ;
        });
    }
}
