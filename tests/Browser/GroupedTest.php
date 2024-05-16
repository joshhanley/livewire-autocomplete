<?php

namespace LivewireAutocomplete\Tests\Browser;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Livewire;

class GroupedTest extends BrowserTestCase
{
    public function component()
    {
        return new class extends Component {
            public $input;
            public $selected;

            #[Computed]
            public function topResults()
            {
                return collect([
                    [
                        'id' => 1,
                        'name' => 'bob',
                    ],
                    [
                        'id' => 3,
                        'name' => 'bill',
                    ],
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

            #[Computed]
            public function allResults()
            {
                return collect([
                    [
                        'id' => 1,
                        'name' => 'bob',
                    ],
                    [
                        'id' => 2,
                        'name' => 'john',
                    ],
                    [
                        'id' => 3,
                        'name' => 'bill',
                    ],
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

                        <x-autocomplete-list dusk="dropdown" x-cloak>
                            <li>Top results</li>
                            
                            @foreach($this->topResults as $index => $result)
                                <x-autocomplete-item key="top-{{ $result['id'] }}" :id="$result['id']" :value="$result['name']" dusk="top-result-{{ $index }}">
                                    {{ $result['name'] }}
                                </x-autocomplete-item>
                            @endforeach

                            <li>All results</li>

                            @foreach($this->allResults as $index => $result)
                                <x-autocomplete-item key="all-{{ $result['id'] }}" :id="$result['id']" :value="$result['name']" dusk="all-result-{{ $index }}">
                                    {{ $result['name'] }}
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
    public function list_items_can_be_given_unique_keys_in_the_scenario_where_an_item_is_displayed_multiple_times_in_different_groupings_to_ensure_it_is_highlighted_correctly()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(101)
            ->assertClassMissing('@top-result-0', 'bg-blue-500')
            ->assertClassMissing('@top-result-1', 'bg-blue-500')
            ->assertClassMissing('@all-result-0', 'bg-blue-500')
            ->assertClassMissing('@all-result-1', 'bg-blue-500')
            ->assertClassMissing('@all-result-2', 'bg-blue-500')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertHasClass('@top-result-0', 'bg-blue-500')
            ->assertClassMissing('@top-result-1', 'bg-blue-500')
            ->assertClassMissing('@all-result-0', 'bg-blue-500')
            ->assertClassMissing('@all-result-1', 'bg-blue-500')
            ->assertClassMissing('@all-result-2', 'bg-blue-500')
            ->keys('@input', '{ARROW_DOWN}')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertClassMissing('@top-result-0', 'bg-blue-500')
            ->assertClassMissing('@top-result-1', 'bg-blue-500')
            ->assertHasClass('@all-result-0', 'bg-blue-500')
            ->assertClassMissing('@all-result-1', 'bg-blue-500')
            ->assertClassMissing('@all-result-2', 'bg-blue-500')
        ;
    }
}
