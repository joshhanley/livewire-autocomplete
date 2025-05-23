<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompleteOptionsTest;

use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\TestCase;

class AutocompleteAutoselectTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function on_autoselect_first_option_is_selected_by_default()
    {
        Livewire::withQueryParams(['autoselect' => true])
            ->visit(PageWithAutoselectOptionComponent::class)
            ->click('@autocomplete-input')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function on_autoselect_up_arrow_stops_on_first_option()
    {
        Livewire::withQueryParams(['autoselect' => true])
            ->visit(PageWithAutoselectOptionComponent::class)
            ->click('@autocomplete-input')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@autocomplete-input', '{ARROW_UP}')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function on_autoselect_down_arrow_stops_on_last_option()
    {
        Livewire::withQueryParams(['autoselect' => true])
            ->visit(PageWithAutoselectOptionComponent::class)
            ->click('@autocomplete-input')
            ->keys('@autocomplete-input', '{END}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertHasClass('@result-2', 'bg-blue-500')
            ->keys('@autocomplete-input', '{ARROW_DOWN}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertHasClass('@result-2', 'bg-blue-500')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function on_autoselect_mouse_out_does_not_deselect_current_option()
    {
        Livewire::withQueryParams(['autoselect' => true])
            ->visit(PageWithAutoselectOptionComponent::class)
            ->click('@autocomplete-input')
            // Have to mouseover or mouseleave won't fire
            ->mouseover('@result-0')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')

            // Mousing over the input is outside the dropdown, so the result should remain highlighted
            ->mouseover('@autocomplete-input')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function on_autoselect_refocus_first_option_selected()
    {
        Livewire::withQueryParams(['autoselect' => true])
            ->visit(PageWithAutoselectOptionComponent::class)
            ->click('@autocomplete-input')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->click('@some-element-other-than-the-input')
            ->click('@autocomplete-input')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function on_autoselect_if_no_results_clear_input_on_selection()
    {
        Livewire::withQueryParams(['autoselect' => true])
            ->visit(PageWithAutoselectOptionComponent::class)
            ->click('@autocomplete-input')
            ->waitForLivewire()->type('@autocomplete-input', 'steve')
            // Pause to allow transitions to run
            ->pause(100)
            ->assertValue('@autocomplete-input', 'steve')
            ->waitForLivewire()->keys('@autocomplete-input', '{ENTER}')
            ->assertValue('@autocomplete-input', '')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function on_autoselect_clear_input_on_escape()
    {
        Livewire::withQueryParams(['autoselect' => true])
            ->visit(PageWithAutoselectOptionComponent::class)
            ->click('@autocomplete-input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@autocomplete-input', 'steve')
            ->assertValue('@autocomplete-input', 'steve')
            ->waitForLivewire()->keys('@autocomplete-input', '{ESCAPE}')
            ->assertValue('@autocomplete-input', '')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function on_autoselect_click_away_clears_input_text()
    {
        Livewire::withQueryParams(['autoselect' => true])
            ->visit(PageWithAutoselectOptionComponent::class)
            ->click('@autocomplete-input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@autocomplete-input', 'steve')
            ->waitForLivewire()->click('@some-element-other-than-the-input')
            ->assertValue('@autocomplete-input', '')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function on_autoselect_click_away_does_not_clear_selected_text()
    {
        Livewire::withQueryParams(['autoselect' => true])
            ->visit(PageWithAutoselectOptionComponent::class)
            ->click('@autocomplete-input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->click('@result-0')
            ->assertValue('@autocomplete-input', 'bob')
            ->click('@some-element-other-than-the-input')
            ->assertValue('@autocomplete-input', 'bob')
        ;
    }
}
