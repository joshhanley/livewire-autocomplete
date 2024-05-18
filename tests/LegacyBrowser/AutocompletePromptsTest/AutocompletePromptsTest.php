<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\AutocompletePromptsTest;

use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class AutocompletePromptsTest extends TestCase
{
    /** @test */
    public function placeholder_text_prompt_is_shown_on_focus()
    {
        Livewire::visit(AutocompletePromptsComponent::class)
            ->click('@autocomplete-input')
            // Pause to allow transitions to run
            ->pause(101)
            ->assertVisible('@autocomplete-dropdown')
            ->assertSeeIn('@autocomplete-dropdown', 'Start typing to search');
    }

    /** @test */
    public function no_results_text_prompt_is_shown_if_nothing_found()
    {
        Livewire::visit(AutocompletePromptsComponent::class)
            ->click('@autocomplete-input')
            // Pause to allow transitions to run
            ->pause(101)
            ->assertVisible('@autocomplete-dropdown')
            ->waitForLivewire()->type('@autocomplete-input', 'a')
            ->assertSeeIn('@autocomplete-dropdown', 'No results found');
    }
}
