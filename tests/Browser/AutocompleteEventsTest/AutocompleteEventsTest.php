<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompleteEventsTest;

use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\TestCase;

class AutocompleteEventsTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function event_is_dispatched_on_input()
    {
        Livewire::visit(PageWithEventsComponent::class)
                ->assertSeeNothingIn('@alpine-input')
                ->click('@autocomplete-input')
                ->waitForLivewire()->type('@autocomplete-input', 'b')
                ->assertSeeIn('@alpine-input', 'b')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function event_is_dispatched_on_selected_item()
    {
        Livewire::visit(PageWithEventsComponent::class)
                ->assertSeeNothingIn('@alpine-selected')
                ->click('@autocomplete-input')
                ->waitForLivewire()->click('@result-1')
                ->assertSeeIn('@alpine-selected', 'john')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function event_is_dispatched_on_selected_item_and_input_is_changed()
    {
        Livewire::visit(PageWithEventsComponent::class)
                ->assertSeeNothingIn('@alpine-input')
                ->assertSeeNothingIn('@alpine-selected')
                ->click('@autocomplete-input')
                ->waitForLivewire()->type('@autocomplete-input', 'b')
                ->assertSeeIn('@alpine-input', 'b')
                ->waitForLivewire()->click('@result-1')
                ->assertSeeIn('@alpine-input', 'bill')
                ->assertSeeIn('@alpine-selected', 'bill')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function event_is_dispatched_when_selected_item_is_cleared()
    {
        Livewire::visit(PageWithEventsComponent::class)
                ->click('@autocomplete-input')
                ->waitForLivewire()->click('@result-1')
                ->assertSeeIn('@alpine-input', 'john')
                ->assertSeeIn('@alpine-selected', 'john')
                ->waitForLivewire()->click('@clear')
                ->assertSeeNothingIn('@alpine-input')
                ->assertSeeNothingIn('@alpine-selected')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function event_is_dispatched_when_input_is_reset()
    {
        Livewire::withQueryParams(['autoselect' => true])
            ->visit(PageWithEventsComponent::class)
                ->assertSeeNothingIn('@alpine-input')
                ->click('@autocomplete-input')
                ->waitForLivewire()->type('@autocomplete-input', 'b')
                ->assertSeeIn('@alpine-input', 'b')
                ->waitForLivewire()->keys('@autocomplete-input', '{ESCAPE}')
                ->assertSeeNothingIn('@alpine-input')
                ->assertDontSeeIn('@alpine-input', 'b')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function selected_is_cleared_when_clear_event_received()
    {
        Livewire::visit(PageWithEventsComponent::class)
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
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function selected_is_set_when_set_event_received()
    {
        Livewire::visit(PageWithEventsComponent::class)
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
    }
}
