<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompleteEventsTest;

use Laravel\Dusk\Browser;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\TestCase;

class AutocompleteEventsTest extends TestCase
{
    /** @test */
    public function event_is_dispatched_on_input()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithEventsComponent::class)
                    ->assertSeeNothingIn('@alpine-input')
                    ->click('@autocomplete-input')
                    ->waitForLivewire()->type('@autocomplete-input', 'b')
                    ->assertSeeIn('@alpine-input', 'b')
                    ;
        });
    }

    /** @test */
    public function event_is_dispatched_on_selected_item()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithEventsComponent::class)
                    ->assertSeeNothingIn('@alpine-selected')
                    ->click('@autocomplete-input')
                    ->waitForLivewire()->click('@result-1')
                    ->assertSeeIn('@alpine-selected', 'john')
                    ;
        });
    }

    /** @test */
    public function event_is_dispatched_on_selected_item_and_input_is_changed()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithEventsComponent::class)
                    ->assertSeeNothingIn('@alpine-input')
                    ->assertSeeNothingIn('@alpine-selected')
                    ->click('@autocomplete-input')
                    ->waitForLivewire()->type('@autocomplete-input', 'b')
                    ->assertSeeIn('@alpine-input', 'b')
                    ->waitForLivewire()->click('@result-1')
                    ->assertSeeIn('@alpine-input', 'bill')
                    ->assertSeeIn('@alpine-selected', 'bill')
                    ;
        });
    }

    /** @test */
    public function event_is_dispatched_when_selected_item_is_cleared()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithEventsComponent::class)
                    ->click('@autocomplete-input')
                    ->waitForLivewire()->click('@result-1')
                    ->assertSeeIn('@alpine-input', 'john')
                    ->assertSeeIn('@alpine-selected', 'john')
                    ->waitForLivewire()->click('@clear')
                    ->assertSeeNothingIn('@alpine-input')
                    ->assertSeeNothingIn('@alpine-selected')
                    ;
        });
    }

    /** @test */
    public function selected_is_cleared_when_clear_event_received()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithEventsComponent::class)
                    ->click('@autocomplete-input')
                    ->waitForLivewire()->click('@result-2')
                    ->assertSeeIn('@result-output', 'bill')
                    ->assertValue('@autocomplete-input', 'bill')
                    ->assertSeeIn('@alpine-input', 'bill')
                    ->assertSeeIn('@alpine-selected', 'bill')
                    ->waitForLivewire()->click('@alpine-clear')
                    ->assertSeeNothingIn('@result-output')
                    ->assertValue('@autocomplete-input', '')
                    ->assertSeeNothingIn('@alpine-input')
                    ->assertSeeNothingIn('@alpine-selected')
                    ;
        });
    }

    /** @test */
    public function selected_is_set_when_set_event_received()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithEventsComponent::class)
                    ->assertSeeNothingIn('@result-output')
                    ->assertValue('@autocomplete-input', '')
                    ->assertSeeNothingIn('@alpine-input')
                    ->assertSeeNothingIn('@alpine-selected')
                    ->waitForLivewire()->click('@alpine-set')
                    ->assertSeeIn('@result-output', 'bob')
                    ->assertValue('@autocomplete-input', 'bob')
                    ->assertSeeIn('@alpine-input', 'bob')
                    ->assertSeeIn('@alpine-selected', 'bob')
                    ;
        });
    }

    /** @test */
    public function options_are_set_when_set_options_event_received()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithEventsComponent::class)
                    ->assertSeeNothingIn('@options')
                    ->waitForLivewire()->click('@alpine-options')
                    ->assertSeeIn('@options', 'filter')
                    ;
        });
    }
}
