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

    /** @test */
    public function default_inline_styles_are_used_when_inline_is_true()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, DefaultStylesComponent::class, '?inline=true')
                ->click('@autocomplete-input')
                ->assertHasClass('@autocomplete-dropdown', 'relative')
                ;
        });
    }

    /** @test */
    public function default_overlay_styles_are_used_when_inline_is_false()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, DefaultStylesComponent::class, '?inline=false')
                ->click('@autocomplete-input')
                ->assertHasClass('@autocomplete-dropdown', 'absolute')
                ->assertHasClass('@autocomplete-dropdown', 'z-30')
                ;
        });
    }

    /** @test */
    public function default_result_focus_styles_are_used()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, DefaultStylesComponent::class, '?inline=false')
                ->click('@autocomplete-input')
                ->keys('@autocomplete-input', '{ARROW_DOWN}')
                ->assertHasClass('@result-0', 'bg-blue-500')
                ;
        });
    }

    /** @test */
    public function custom_inline_styles_are_used_when_inline_is_true()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, CustomStylesComponent::class, '?inline=true')
                ->click('@autocomplete-input')
                ->assertHasClass('@autocomplete-dropdown', 'some-inline-style')
                ->assertClassMissing('@autocomplete-dropdown', 'relative')
                ;
        });
    }

    /** @test */
    public function custom_overlay_styles_are_used_when_inline_is_false()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, CustomStylesComponent::class, '?inline=false')
                ->click('@autocomplete-input')
                ->assertHasClass('@autocomplete-dropdown', 'some-overlay-style')
                ->assertClassMissing('@autocomplete-dropdown', 'absolute')
                ->assertClassMissing('@autocomplete-dropdown', 'z-30')
                ;
        });
    }

    /** @test */
    public function custom_result_focus_styles_are_used()
    {
        $this->browse(function (Browser $browser) {
            Livewire::visit($browser, CustomStylesComponent::class, '?inline=false')
                ->click('@autocomplete-input')
                ->keys('@autocomplete-input', '{ARROW_DOWN}')
                ->assertHasClass('@result-0', 'some-focus-style')
                ->assertClassMissing('@result-0', 'bg-blue-500')
                ;
        });
    }
}
