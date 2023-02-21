<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompleteLoadingTest;

use Laravel\Dusk\Browser;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\TestCase;

class AutocompleteLoadingTest extends TestCase
{
    /** @test */
    public function loading_indicator_appears_when_request_is_taking_too_long()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, AutocompleteWithLoadingComponent::class)
                    ->assertMissing('@autocomplete-dropdown')
                    ->click('@autocomplete-input')
                    ->assertMissing('@autocomplete-loading')
                    ->type('@autocomplete-input', 'b')
                    // Wait for loading indicator to show up
                    ->pause(700)
                    ->assertVisible('@autocomplete-loading')
                    ;
        });
    }
}
