<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\AutocompleteEventsTest;

use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class AutocompleteEventsTest extends TestCase
{
    /** @test */
    public function event_is_dispatched_on_selected_item()
    {
        Livewire::visit(PageWithEventsComponent::class)
            ->assertSeeNothingIn('@alpine-selected')
            ->click('@autocomplete-input')
            ->waitForLivewire()->click('@result-1')
            ->assertSeeIn('@alpine-selected', 'john');
    }

    /** @test */
    public function event_is_dispatched_on_selected_item_and_input_is_changed()
    {
        Livewire::visit(PageWithEventsComponent::class)
            ->assertSeeNothingIn('@alpine-selected')
            ->click('@autocomplete-input')
            ->waitForLivewire()->type('@autocomplete-input', 'b')
            ->waitForLivewire()->click('@result-1')
            ->assertSeeIn('@alpine-selected', 'bill');
    }

    /** @test */
    public function event_is_dispatched_when_selected_item_is_cleared()
    {
        Livewire::visit(PageWithEventsComponent::class)
            ->click('@autocomplete-input')
            ->waitForLivewire()->click('@result-1')
            ->assertSeeIn('@alpine-selected', 'john')
            ->waitForLivewire()->click('@clear')
            ->assertSeeNothingIn('@alpine-selected');
    }

    /** @test */
    public function selected_is_cleared_when_clear_event_received()
    {
        Livewire::visit(PageWithEventsComponent::class)
            ->click('@autocomplete-input')
            ->waitForLivewire()->click('@result-2')
            ->assertSeeIn('@result-output', 'bill')
            ->assertValue('@autocomplete-input', 'bill')
            ->assertSeeIn('@alpine-selected', 'bill')
            ->waitForLivewire()->click('@alpine-clear')
            ->assertSeeNothingIn('@result-output')
            ->assertValue('@autocomplete-input', '')
            ->assertSeeNothingIn('@alpine-selected');
    }
}
