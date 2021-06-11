<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompleteOptionsTest;

use Laravel\Dusk\Browser;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\TestCase;

class AutocompleteAutoselectTest extends TestCase
{
    /** @test */
    public function on_autoselect_first_option_is_selected_by_default()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutoselectOptionComponent::class, '?autoselect=true')
                ->click('@autocomplete-input')
                ->assertHasClass('@result-0', 'bg-blue-500')
                ->assertClassMissing('@result-1', 'bg-blue-500')
                ->assertClassMissing('@result-2', 'bg-blue-500')
                ;
        });
    }

    /** @test */
    public function on_autoselect_up_arrow_stops_on_first_option()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutoselectOptionComponent::class, '?autoselect=true')
                ->click('@autocomplete-input')
                ->assertHasClass('@result-0', 'bg-blue-500')
                ->assertClassMissing('@result-1', 'bg-blue-500')
                ->assertClassMissing('@result-2', 'bg-blue-500')
                ->keys('@autocomplete-input', '{ARROW_UP}')
                ->assertHasClass('@result-0', 'bg-blue-500')
                ->assertClassMissing('@result-1', 'bg-blue-500')
                ->assertClassMissing('@result-2', 'bg-blue-500')
                ;
        });
    }

    /** @test */
    public function on_autoselect_down_arrow_stops_on_last_option()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutoselectOptionComponent::class, '?autoselect=true')
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
        });
    }

    /** @test */
    public function on_autoselect_mouse_out_does_not_deselect_current_option()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutoselectOptionComponent::class, '?autoselect=true')
                ->click('@autocomplete-input')
                //Have to mouseover or mouseleave won't fire
                ->mouseover('@result-1')
                ->assertClassMissing('@result-0', 'bg-blue-500')
                ->assertHasClass('@result-1', 'bg-blue-500')
                ->assertClassMissing('@result-2', 'bg-blue-500')
                //Empty mouseover simulates mouseout by mousing over body
                ->mouseover('')
                ->assertClassMissing('@result-0', 'bg-blue-500')
                ->assertHasClass('@result-1', 'bg-blue-500')
                ->assertClassMissing('@result-2', 'bg-blue-500')
                ;
        });
    }

    /** @test */
    public function on_autoselect_refocus_first_option_selected()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutoselectOptionComponent::class, '?autoselect=true')
                ->click('@autocomplete-input')
                ->assertHasClass('@result-0', 'bg-blue-500')
                ->assertClassMissing('@result-1', 'bg-blue-500')
                ->assertClassMissing('@result-2', 'bg-blue-500')
                ->clickAtXPath('//body')
                ->click('@autocomplete-input')
                ->assertHasClass('@result-0', 'bg-blue-500')
                ->assertClassMissing('@result-1', 'bg-blue-500')
                ->assertClassMissing('@result-2', 'bg-blue-500')
                ;
        });
    }

    /** @test */
    public function on_autoselect_if_no_results_clear_input_on_selection()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutoselectOptionComponent::class, '?autoselect=true')
                ->click('@autocomplete-input')
                ->waitForLivewire()->type('@autocomplete-input', 'steve')
                // Pause to allow transitions to run
                ->pause(100)
                ->assertValue('@autocomplete-input', 'steve')
                ->waitForLivewire()->keys('@autocomplete-input', '{ENTER}')
                ->assertValue('@autocomplete-input', '')
                ;
        });
    }

    /** @test */
    public function on_autoselect_clear_input_on_escape()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutoselectOptionComponent::class, '?autoselect=true')
                ->click('@autocomplete-input')
                // Pause to allow transitions to run
                ->pause(100)
                ->waitForLivewire()->type('@autocomplete-input', 'steve')
                ->assertValue('@autocomplete-input', 'steve')
                ->waitForLivewire()->keys('@autocomplete-input', '{ESCAPE}')
                ->assertValue('@autocomplete-input', '')
                ;
        });
    }

    /** @test */
    public function on_autoselect_click_away_clears_input_text()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutoselectOptionComponent::class, '?autoselect=true')
                ->click('@autocomplete-input')
                // Pause to allow transitions to run
                ->pause(100)
                ->waitForLivewire()->type('@autocomplete-input', 'steve')
                ->waitForLivewire()->clickAtXPath('//body')
                ->assertValue('@autocomplete-input', '')
                ;
        });
    }

    /** @test */
    public function on_autoselect_click_away_does_not_clear_selected_text()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutoselectOptionComponent::class, '?autoselect=true')
                ->click('@autocomplete-input')
                // Pause to allow transitions to run
                ->pause(100)
                ->waitForLivewire()->click('@result-0')
                ->assertValue('@autocomplete-input', 'bob')
                ->clickAtXPath('//body')
                ->assertValue('@autocomplete-input', 'bob')
                ;
        });
    }
}
