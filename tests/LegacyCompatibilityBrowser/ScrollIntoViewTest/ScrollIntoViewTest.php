<?php

namespace LivewireAutocomplete\Tests\LegacyCompatibilityBrowser\ScrollIntoViewTest;

use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class ScrollIntoViewTest extends TestCase
{
    /** @test */
    public function it_shows_custom_component_when_passed_into_the_instance_through_props()
    {
        Livewire::visit(ScrollIntoViewTestComponent::class)
            ->click('@autocomplete-input')
            ->isVisibleInContainer('@result-1', '@autocomplete-dropdown')
            ->isNotVisibleInContainer('@result-12', '@autocomplete-dropdown')
            ->keys('@autocomplete-input', '{END}')
            // Need to wait long enough for native scroll animation to happen
            ->pause(400)
            ->isVisibleInContainer('@result-12', '@autocomplete-dropdown')
            ->isNotVisibleInContainer('@result-1', '@autocomplete-dropdown');
    }
}
