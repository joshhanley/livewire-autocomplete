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
}
