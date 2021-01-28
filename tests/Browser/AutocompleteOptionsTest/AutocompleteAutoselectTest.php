<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompleteOptionsTest;

use Laravel\Dusk\Browser;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\TestCase;

class AutocompleteAutoselectTest extends TestCase
{
    /** @test */
    public function first_option_is_selected_if_autoselect_flag_is_set_to_true()
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
}
