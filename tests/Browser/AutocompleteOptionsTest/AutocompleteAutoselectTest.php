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
}
