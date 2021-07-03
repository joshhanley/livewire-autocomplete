<?php

namespace LivewireAutocomplete\Tests\Browser\OptionsTest;

use Laravel\Dusk\Browser;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\TestCase;

class OptionsTest extends TestCase
{
    /** @test */
    public function custom_attribute_names_can_be_passed_in_via_options()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, CustomAttributesComponent::class)
                ->click('@autocomplete-input')
                ->waitForLivewire()->click('@result-1')
                ->assertSeeIn('@input-text-output', 'john')
                ->assertSeeIn('@selected-slug-output', 'B')
                ;
        });
    }
}
