<?php

namespace LivewireAutocomplete\Tests\Browser;

use Laravel\Dusk\Browser;
use Livewire\Livewire;

class AutocompleteTest extends TestCase
{
    /** @test */
    public function an_input_is_shown_on_screen()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->assertPresent('@autocomplete-input')
                    ;
        });
    }

    /** @test */
    public function dropdown_appears_when_input_is_focused()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->assertMissing('@autocomplete-dropdown')
                    ->click('@autocomplete-input')
                    ->assertVisible('@autocomplete-dropdown')
                    ;
        });
    }

    /** @test */
    public function dropdown_closes_when_anything_else_is_clicked_and_focus_is_removed()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertVisible('@autocomplete-dropdown')
                    ->clickAtXPath('//body')
                    ->assertNotFocused('@autocomplete-input')
                    ->assertMissing('@autocomplete-dropdown')
                    ;
        });
    }

    /** @test */
    public function dropdown_closes_when_escape_is_pressed_and_focus_removed()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertVisible('@autocomplete-dropdown')
                    ->keys('@autocomplete-input', '{escape}')
                    ->assertNotFocused('@autocomplete-input')
                    ->assertMissing('@autocomplete-dropdown')
                    ;
        });
    }

    /** @test */
    public function dropdown_shows_list_of_results()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertSeeIn('@autocomplete-dropdown', 'bob')
                    ->assertSeeIn('@autocomplete-dropdown', 'john')
                    ->assertSeeIn('@autocomplete-dropdown', 'bill')
                    ->assertSeeInOrder('@autocomplete-dropdown', ['bob', 'john', 'bill'])
                    ;
        });
    }

    /** @test */
    public function results_are_filtered_based_on_input()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertSeeInOrder('@autocomplete-dropdown', ['bob', 'john', 'bill'])
                    ->waitForLivewire()->type('@autocomplete-input', 'b')
                    ->assertSeeInOrder('@autocomplete-dropdown', ['bob', 'bill'])
                    ->assertDontSeeIn('@autocomplete-dropdown', 'john')
                    ;
        });
    }
}
