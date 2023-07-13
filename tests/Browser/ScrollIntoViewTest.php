<?php

namespace LivewireAutocomplete\Tests\Browser;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Livewire;

class ScrollIntoViewTest extends BrowserTestCase
{
    public function component()
    {
        return new class extends Component {
            public $input;
            public $selected;

            #[Computed]
            public function results()
            {
                return collect([
                    'sample1',
                    'sample2',
                    'sample3',
                    'sample4',
                    'sample5',
                    'sample6',
                    'sample7',
                    'sample8',
                    'sample9',
                    'sample10',
                    'sample11',
                    'sample12',
                    'sample13',
                ])
                    ->filter(function ($result) {
                        if (! $this->input) {
                            return true;
                        }

                        return str_contains($result['name'], $this->input);
                    })
                    ->values()
                    ->toArray();
            }

            public function render()
            {
                return <<< 'HTML'
                <div>
                    <x-autocomplete wire:model.live="selected">
                        <x-autocomplete-input wire:model.live="input" dusk="input" />

                        <x-autocomplete-list style="height: 50px; overflow: scroll;" dusk="dropdown" x-cloak>
                            @foreach($this->results as $index => $result)
                                <x-autocomplete-item :key="$result" :value="$result" dusk="result-{{ $index }}">
                                    {{ $result }}
                                </x-autocomplete-item>
                            @endforeach
                        </x-autocomplete-list>
                    </x-autocomplete>

                    <div>Selected: <span dusk="selected-output">{{ $selected }}</span></div>
                    <div>Input: <span dusk="input-output">{{ $input }}</span></div>
                </div>
                HTML;
            }
        };
    }

    /** @test */
    public function it_shows_custom_component_when_passed_into_the_instance_through_props()
    {
        Livewire::visit($this->component())
            ->click('@input')
            ->isVisibleInContainer('@result-1', '@dropdown')
            ->isNotVisibleInContainer('@result-12', '@dropdown')
            ->keys('@input', '{END}')
            // Need to wait long enough for native scroll animation to happen
            ->pause(400)
            ->isVisibleInContainer('@result-12', '@dropdown')
            ->isNotVisibleInContainer('@result-1', '@dropdown')
        ;
    }
}
