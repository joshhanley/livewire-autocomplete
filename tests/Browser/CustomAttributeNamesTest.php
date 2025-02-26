<?php

namespace LivewireAutocomplete\Tests\Browser;

use Livewire\Component;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class CustomAttributeNamesTest extends TestCase
{
    public function defaultComponent()
    {
        return new class extends Component
        {
            public $results = [
                [
                    'slug' => 'A',
                    'name' => 'bob',
                ],
                [
                    'slug' => 'B',
                    'name' => 'john',
                ],
                [
                    'slug' => 'C',
                    'name' => 'bill',
                ],
            ];

            public $inputText = '';

            public $selectedSlug;

            public function render()
            {
                return <<<'HTML'
                    <div dusk="page">
                        <x-autocomplete wire:model.live="selectedSlug">
                            <x-autocomplete.input wire:model.live="inputText" dusk="input" />

                            <x-autocomplete.list dusk="dropdown">
                                @foreach($this->results as $index => $result)
                                    <x-autocomplete.item :key="$result['slug']" :value="$result['name']" dusk="result-{{ $index }}">
                                        {{ $result['name'] }}
                                    </x-autocomplete.item>
                                @endforeach
                            </x-autocomplete.list>
                        </x-autocomplete>

                        <div dusk="input-text-output">{{ $inputText }}</div>
                        <div dusk="selected-slug-output">{{ $selectedSlug }}</div>
                    </div>
                    HTML;
            }
        };
    }

    /** @test */
    public function custom_attribute_names_can_be_passed_in_via_options()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->waitForLivewire()->click('@result-1')
            ->assertValue('@input', 'john')
            ->assertSeeIn('@input-text-output', 'john')
            ->assertSeeIn('@selected-slug-output', 'B');
    }
}
