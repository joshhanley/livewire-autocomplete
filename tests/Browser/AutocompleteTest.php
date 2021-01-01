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
                    ->with('@page', function ($page) {
                        $page->assertPresent('input');
                    })
                    ;
        });
    }

    /** @test */
    public function dropdown_appears_when_input_is_focused()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->with('@page', function ($page) {
                        $page->assertMissing('@dropdown')
                            ->click('input')
                            ->assertVisible('@dropdown')
                            ;
                    })
                    ;
        });
    }
}
