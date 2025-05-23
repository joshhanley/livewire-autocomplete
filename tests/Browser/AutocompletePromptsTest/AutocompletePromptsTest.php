<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompletePromptsTest;

use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\TestCase;

class AutocompletePromptsTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function placeholder_text_prompt_is_shown_on_focus()
    {
        Livewire::visit(AutocompletePromptsComponent::class)
            ->click('@autocomplete-input')
            // Pause to allow transitions to run
            ->pause(101)
            ->assertVisible('@autocomplete-dropdown')
            ->assertSeeIn('@autocomplete-dropdown', 'Start typing to search')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function no_results_text_prompt_is_shown_if_nothing_found()
    {
        Livewire::visit(AutocompletePromptsComponent::class)
            ->click('@autocomplete-input')
            // Pause to allow transitions to run
            ->pause(101)
            ->assertVisible('@autocomplete-dropdown')
            ->waitForLivewire()->type('@autocomplete-input', 'a')
            ->assertSeeIn('@autocomplete-dropdown', 'No results found')
        ;
    }
}
