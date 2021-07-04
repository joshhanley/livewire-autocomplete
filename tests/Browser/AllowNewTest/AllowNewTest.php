<?php

namespace LivewireAutocomplete\Tests\Browser\AllowNewTest;

use Laravel\Dusk\Browser;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\TestCase;

class AllowNewTest extends TestCase
{
    /** @test */
    public function add_new_row_is_not_shown_when_there_is_no_input()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, AddNewItemComponent::class)
                ->click('@autocomplete-input')
                // Pause to allow transitions to run
                ->pause(100)
                ->assertNotPresent('@add-new')
                ;
        });
    }

    /** @test */
    public function add_new_row_appears_when_allow_new_is_true_and_text_is_entered()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, AddNewItemComponent::class)
                ->click('@autocomplete-input')
                // Pause to allow transitions to run
                ->pause(100)
                ->waitForLivewire()->type('@autocomplete-input', 'b')
                ->assertPresent('@add-new')
                ->assertSeeIn('@add-new', 'Add new "b"')
                ;
        });
    }

    /** @test */
    public function add_new_row_is_visible_when_no_results_found()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, AddNewItemComponent::class)
                ->click('@autocomplete-input')
                // Pause to allow transitions to run
                ->pause(100)
                ->waitForLivewire()->type('@autocomplete-input', 'greg')
                ->assertPresent('@add-new')
                ->assertSeeIn('@add-new', 'Add new "greg"')
                ;
        });
    }

    /** @test */
    public function first_result_should_be_highlighted_when_add_new_row_not_displayed_yet()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, AddNewItemComponent::class)
                ->click('@autocomplete-input')
                // Pause to allow transitions to run
                ->pause(100)
                ->assertHasClass('@result-0', 'bg-blue-500')
                ;
        });
    }

    /** @test */
    public function add_new_row_should_be_highlighed_by_default()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, AddNewItemComponent::class)
                ->click('@autocomplete-input')
                // Pause to allow transitions to run
                ->pause(100)
                ->waitForLivewire()->type('@autocomplete-input', 'b')
                ->assertHasClass('@add-new', 'bg-blue-500')
                ;
        });
    }

    /** @test */
    public function add_new_row_should_be_highlighed_after_arrowing_down_and_back_up()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, AddNewItemComponent::class)
                ->click('@autocomplete-input')
                // Pause to allow transitions to run
                ->pause(100)
                ->waitForLivewire()->type('@autocomplete-input', 'b')
                ->assertHasClass('@add-new', 'bg-blue-500')
                ->keys('@autocomplete-input', '{ARROW_DOWN}')
                ->keys('@autocomplete-input', '{ARROW_DOWN}')
                ->assertClassMissing('@add-new', 'bg-blue-500')
                ->assertHasClass('@result-1', 'bg-blue-500')
                ->keys('@autocomplete-input', '{ARROW_UP}')
                ->keys('@autocomplete-input', '{ARROW_UP}')
                ->assertHasClass('@add-new', 'bg-blue-500')
                ->assertClassMissing('@result-1', 'bg-blue-500')
                ;
        });
    }

    /** @test */
    public function first_result_should_be_selected_when_add_new_row_not_displayed_yet()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, AddNewItemComponent::class)
                ->click('@autocomplete-input')
                // Pause to allow transitions to run
                ->pause(100)
                ->waitForLivewire()->keys('@autocomplete-input', '{TAB}')
                ->assertSeeIn('@selected-slug-output', '1')
                ->assertSeeIn('@input-text-output', 'bob')
                ;
        });
    }

    /** @test */
    public function add_new_row_should_be_selected_but_nothing_happen()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, AddNewItemComponent::class)
                ->click('@autocomplete-input')
                // Pause to allow transitions to run
                ->pause(100)
                ->waitForLivewire()->type('@autocomplete-input', 'j')
                ->keys('@autocomplete-input', '{TAB}')
                // Pause to allow Livewire to run if it was going to
                ->pause(100)
                ->assertSeeNothingIn('@selected-slug-output')
                ->assertSeeIn('@input-text-output', 'j')
                ;
        });
    }

    /** @test */
    public function highlighted_record_should_be_selected_even_when_add_new_row_displayed()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, AddNewItemComponent::class)
                ->click('@autocomplete-input')
                // Pause to allow transitions to run
                ->pause(100)
                ->waitForLivewire()->type('@autocomplete-input', 'j')
                ->keys('@autocomplete-input', '{ARROW_DOWN}')
                ->waitForLivewire()->keys('@autocomplete-input', '{TAB}')
                ->assertSeeIn('@selected-slug-output', '2')
                ->assertSeeIn('@input-text-output', 'j')
                ;
        });
    }
}
