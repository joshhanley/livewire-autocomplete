<?php

namespace LivewireAutocomplete\Tests\Browser\LoadOnFocusTest;

use Livewire\Livewire;
use LivewireAutocomplete\Tests\Browser\TestCase;

class LoadOnFocusTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function results_are_not_loaded_initially()
    {
        Livewire::visit(LoadOnFocusComponent::class)
            ->assertSeeIn('@number-times-calculate-called', 0)
            ->assertDontSeeIn('@autocomplete-dropdown', 'bob')
            ->assertDontSeeIn('@autocomplete-dropdown', 'john')
            ->assertDontSeeIn('@autocomplete-dropdown', 'bill')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_loads_results_on_focus_if_action_is_present()
    {
        Livewire::visit(LoadOnFocusComponent::class)
            ->waitForLivewire()->click('@autocomplete-input')
            ->assertSeeIn('@number-times-calculate-called', 1)
            ->assertSeeIn('@autocomplete-dropdown', 'bob')
            ->assertSeeIn('@autocomplete-dropdown', 'john')
            ->assertSeeIn('@autocomplete-dropdown', 'bill')
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_only_loads_results_once_if_load_once_on_focus_is_set_to_true()
    {
        Livewire::visit(LoadOnFocusComponent::class)
            ->waitForLivewire()->click('@autocomplete-input')
            ->assertSeeIn('@number-times-calculate-called', 1)
            ->keys('@autocomplete-input', '{ESCAPE}')
            ->click('@autocomplete-input')
            // Wait for livewire request if it was going to happen (it shouldn't)
            ->pause(100)
            ->assertSeeIn('@number-times-calculate-called', 1)
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_loads_results_on_every_focus_if_load_once_on_focus_is_set_to_false()
    {
        Livewire::withQueryParams(['loadOnceOnFocus' => false])
            ->visit(LoadOnFocusComponent::class)
            ->waitForLivewire()->click('@autocomplete-input')
            ->assertSeeIn('@number-times-calculate-called', 1)
            ->keys('@autocomplete-input', '{ESCAPE}')
            ->waitForLivewire()->click('@autocomplete-input')
            ->assertSeeIn('@number-times-calculate-called', 2)
        ;
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_call_focus_method_with_parameters()
    {
        Livewire::withQueryParams(['loadOnceOnFocus' => false, 'useParameters' => true])
            ->visit(LoadOnFocusComponent::class)
            ->assertSeeNothingIn('@parameter-1-value')
            ->assertSeeNothingIn('@parameter-2-value')
            ->waitForLivewire()->click('@autocomplete-input')
            ->assertSeeIn('@parameter-1-value', 'some-parameter')
            ->assertSeeIn('@parameter-2-value', 'other-parameter')
        ;
    }
}
