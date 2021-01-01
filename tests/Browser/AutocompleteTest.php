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

    /** @test */
    public function dropdown_closes_when_anything_else_is_clicked_and_focus_is_removed()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->with('@page', function ($page) {
                        $page->click('input')
                            ->assertVisible('@dropdown')
                            ->clickAtXPath('//body')
                            ->assertNotFocused('input')
                            ->assertMissing('@dropdown')
                            ;
                    })
                    ;
        });
    }

    /** @test */
    public function dropdown_closes_when_escape_is_pressed_and_focus_removed()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, PageWithAutocompleteComponent::class)
                    ->with('@page', function ($page) {
                        $page->click('input')
                            ->assertVisible('@dropdown')
                            ->keys('input', '{escape}')
                            ->assertNotFocused('input')
                            ->assertMissing('@dropdown')
                            ;
                    })
                    ;
        });
    }
}
