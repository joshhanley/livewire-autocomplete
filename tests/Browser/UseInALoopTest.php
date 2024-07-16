<?php

namespace LivewireAutocomplete\Tests\Browser;

use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class UseInALoopTest extends TestCase
{
    public function component()
    {
        return new class extends Component
        {
            public Collection $selectedNames;

            public function mount()
            {
                $this->selectedNames = new Collection();

                $this->addRow();
            }

            public function addRow()
            {
                $this->selectedNames->push([
                    'search' => null,
                    'selected' => null,
                ]);
            }

            public function removeRow($index)
            {
                $this->selectedNames->forget($index);

                $this->selectedNames = $this->selectedNames->values();
            }

            public function results($index)
            {
                return collect([
                    'bob',
                    'john',
                    'bill',
                    'jane',
                    'steve',
                ])
                    ->filter(function ($result) use ($index) {
                        if (! $this->selectedNames[$index]['search']) {
                            return true;
                        }

                        return str_contains($result, $this->selectedNames[$index]['search']);
                    })
                    ->values()
                    ->sort()
                    ->toArray();
            }

            public function render()
            {
                return <<< 'HTML'
                <div>
                    <div>
                        @foreach ($this->selectedNames as $index => $selectedName)
                            <div>
                                <x-autocomplete wire:model.live="selectedNames.{{ $index }}.selected" dusk="autocomplete-{{ $index }}">
                                    <x-autocomplete-input wire:model.live="selectedNames.{{ $index }}.search" dusk="input-{{ $index }}" />
                                    <x-autocomplete-list dusk="dropdown-{{ $index }}" x-cloak>
                                        @foreach($this->results($index) as $key => $result)
                                            <x-autocomplete-item :key="$result" :value="$result" dusk="result-{{ $index }}-{{ $key }}">
                                                {{ $result }}
                                            </x-autocomplete-item>
                                        @endforeach
                                    </x-autocomplete-list>
                                </x-autocomplete>

                                <button type="button" wire:click="removeRow({{ $index }})" dusk="remove-row-{{ $index }}">Remove</button>
                            </div>

                            <div dusk="result-output-{{ $index }}">{{ $selectedName['selected'] }}</div>
                        @endforeach
                    </div>

                    <button type="button" wire:click="addRow" dusk="add-row">Add Row</button>

                    <div>
                        Output: {{ json_encode($this->selectedNames) }}
                    </div>
                </div>
                HTML;
            }
        };
    }

    /** @test */
    public function autocomplete_can_be_used_in_a_loop_without_errors()
    {
        Livewire::visit($this->component())
            ->assertPresent('@autocomplete-0')
            ->assertNotPresent('@autocomplete-1')
            ->assertNotPresent('@autocomplete-2')
            ->click('@input-0')
            ->assertVisible('@dropdown-0')
            ->waitForLivewire()->click('@result-0-0')
            ->assertSeeIn('@result-output-0', 'bob')

            ->waitForLivewire()->click('@add-row')
            ->assertPresent('@autocomplete-0')
            ->assertPresent('@autocomplete-1')
            ->assertNotPresent('@autocomplete-2')
            ->click('@input-1')
            ->assertVisible('@dropdown-1')
            ->waitForLivewire()->click('@result-1-1')
            ->assertSeeIn('@result-output-1', 'john')

            ->waitForLivewire()->click('@add-row')
            ->assertPresent('@autocomplete-0')
            ->assertPresent('@autocomplete-1')
            ->assertPresent('@autocomplete-2')
            ->click('@input-2')
            ->assertVisible('@dropdown-2')
            ->waitForLivewire()->click('@result-2-2')
            ->assertSeeIn('@result-output-2', 'bill')

            ->waitForLivewire()->click('@remove-row-1')
            ->assertPresent('@autocomplete-0')
            ->assertPresent('@autocomplete-1')
            ->assertNotPresent('@autocomplete-2')
            ->assertSeeIn('@result-output-0', 'bob')
            ->assertSeeIn('@result-output-1', 'bill')
            ->assertNotPresent('@result-output-2')

            ->click('@input-0')
            ->assertVisible('@dropdown-0')
            ->click('@input-1')
            ->assertVisible('@dropdown-1')
            ->assertConsoleLogMissingErrors();
    }
}
