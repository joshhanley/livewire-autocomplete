<?php

namespace LivewireAutocomplete\Tests\Browser\OptionsTest;

use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\TestCase;

class OptionsTest extends TestCase
{
    /** @test */
    public function custom_attribute_names_can_be_passed_in_via_options()
    {
        Livewire::visit(CustomAttributesComponent::class)
            ->click('@autocomplete-input')
            ->waitForLivewire()->click('@result-1')
            ->assertSeeIn('@input-text-output', 'john')
            ->assertSeeIn('@selected-slug-output', 'B')
        ;
    }

    /** @test */
    public function default_inline_styles_are_used_when_inline_is_true()
    {
        Livewire::withQueryParams(['inline' => true])
            ->visit(DefaultStylesComponent::class)
            ->click('@autocomplete-input')
            ->assertHasClass('@autocomplete-dropdown', 'relative')
        ;
    }

    /** @test */
    public function default_overlay_styles_are_used_when_inline_is_false()
    {
        Livewire::withQueryParams(['inline' => false])
            ->visit(DefaultStylesComponent::class)
            ->click('@autocomplete-input')
            ->assertHasClass('@autocomplete-dropdown', 'absolute')
            ->assertHasClass('@autocomplete-dropdown', 'z-30')
        ;
    }

    /** @test */
    public function default_result_focus_styles_are_used()
    {
        Livewire::withQueryParams(['inline' => false])
            ->visit(DefaultStylesComponent::class)
            ->click('@autocomplete-input')
            ->keys('@autocomplete-input', '{ARROW_DOWN}')
            ->assertHasClass('@result-0', 'bg-blue-500')
        ;
    }

    /** @test */
    public function custom_inline_styles_are_used_when_inline_is_true()
    {
        Livewire::withQueryParams(['inline' => true])
            ->visit(CustomStylesComponent::class)
            ->click('@autocomplete-input')
            ->assertHasClass('@autocomplete-dropdown', 'some-inline-style')
            ->assertClassMissing('@autocomplete-dropdown', 'relative')
        ;
    }

    /** @test */
    public function custom_overlay_styles_are_used_when_inline_is_false()
    {
        Livewire::withQueryParams(['inline' => false])
            ->visit(CustomStylesComponent::class)
            ->click('@autocomplete-input')
            ->assertHasClass('@autocomplete-dropdown', 'some-overlay-style')
            ->assertClassMissing('@autocomplete-dropdown', 'absolute')
            ->assertClassMissing('@autocomplete-dropdown', 'z-30')
        ;
    }

    /** @test */
    public function custom_result_focus_styles_are_used()
    {
        Livewire::withQueryParams(['inline' => false])
            ->visit(CustomStylesComponent::class)
            ->click('@autocomplete-input')
            ->keys('@autocomplete-input', '{ARROW_DOWN}')
            ->assertHasClass('@result-0', 'some-focus-style')
            ->assertClassMissing('@result-0', 'bg-blue-500')
        ;
    }
}
