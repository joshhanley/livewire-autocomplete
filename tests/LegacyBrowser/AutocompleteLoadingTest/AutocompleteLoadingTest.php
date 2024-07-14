<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\AutocompleteLoadingTest;

use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class AutocompleteLoadingTest extends TestCase
{
    /** @test */
    public function loading_indicator_appears_when_request_is_taking_too_long()
    {
        Livewire::visit(AutocompleteWithLoadingComponent::class)
            ->assertMissing('@autocomplete-dropdown')
            ->click('@autocomplete-input')
            ->assertMissing('@loading')
            ->type('@autocomplete-input', 'b')
                // Wait for loading indicator to show up
            ->pause(700)
            ->assertVisible('@loading');
    }
}
