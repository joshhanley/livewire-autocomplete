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

    /** @test */
    public function down_arrow_focus_first_option_if_there_is_no_focus_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function down_arrow_focus_next_option_if_there_is_already_a_focus_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertHasClass('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function down_arrow_focus_remains_on_last_result_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertHasClass('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertHasClass('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function up_arrow_clears_focus_if_first_option_is_focused_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{ARROW_UP}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function up_arrow_focuses_previous_option_if_there_is_another_option_before_current_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertHasClass('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{ARROW_UP}')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function up_arrow_focuses_nothing_if_nothing_currently_focused_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{ARROW_UP}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function home_key_focuses_first_result_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    // Attempt if none selected
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{HOME}')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')

                    // Attempt if one further down the list is selected
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->keys('@autocomplete-input', '{ARROW_DOWN}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertHasClass('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{HOME}')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ;
        });
    }

    /** @test */
    public function end_key_focuses_last_result_in_dropdown()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->click('@autocomplete-input')
                    // Attempt if none selected
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{END}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertHasClass('@result-2', 'bg-blue-500')

                    // Attempt if one further down the list is selected
                    ->keys('@autocomplete-input', '{ARROW_UP}')
                    ->keys('@autocomplete-input', '{ARROW_UP}')
                    ->assertHasClass('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertClassMissing('@result-2', 'bg-blue-500')
                    ->keys('@autocomplete-input', '{END}')
                    ->assertClassMissing('@result-0', 'bg-blue-500')
                    ->assertClassMissing('@result-1', 'bg-blue-500')
                    ->assertHasClass('@result-2', 'bg-blue-500')
                    ;
        });
    }
}
