<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\ScrollIntoViewTest;

use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class ScrollIntoViewTest extends TestCase
{
    /** @test */
    public function it_shows_custom_component_when_passed_into_the_instance_through_props()
    {
        Livewire::visit(ScrollIntoViewTestComponent::class)
            ->click('@autocomplete-input')
            ->assertIsVisibleInContainer('@autocomplete-dropdown', '@result-1')
            ->assertIsNotVisibleInContainer('@autocomplete-dropdown', '@result-12')
            ->keys('@autocomplete-input', '{END}')
            // Need to wait long enough for native scroll animation to happen
            ->pause(400)
            ->assertIsVisibleInContainer('@autocomplete-dropdown', '@result-12')
            ->assertIsNotVisibleInContainer('@autocomplete-dropdown', '@result-1');
    }
}
