<?php

namespace LivewireAutocomplete\Tests\Browser;

use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class EventsTest extends TestCase
{
    public function component()
    {
        return new class extends Component
        {
            public $results = [
                'bob',
                'john',
                'bill',
            ];

            public $input = '';

            public $selected;

            public $autoselect = false;

            protected $queryString = ['autoselect' => ['except' => false]];

            public function calculateResults()
            {
                $this->reset('results');

                $this->results = Collection::wrap($this->results)
                    ->filter(function ($result) {
                        if (! $this->input) {
                            return true;
                        }

                        return str_contains($result, $this->input);
                    })
                    ->values()
                    ->toArray();
            }

            public function updatedInput()
            {
                $this->calculateResults();
            }

            public function updatedSelected()
            {
                $this->input = $this->selected ?? null;

                $this->calculateResults();
            }

            public function render()
            {
                return <<<'HTML'
                    <div dusk="page">
                        <div>
                            <x-autocomplete
                                :autoSelect="$autoselect"
                                name="item"
                                wire:model.live="selected">
                                <x-autocomplete-input wire:model.live="input" dusk="input">
                                    <x-autocomplete-clear-button dusk="clear-button" />
                                </x-autocomplete-input>

                                <x-autocomplete-list dusk="dropdown" x-cloak>
                                    @unless($autoselect)
                                        <x-autocomplete-new-item :value="$input" dusk="new-item" />
                                    @endunless

                                    @foreach($this->results as $key => $result)
                                        <x-autocomplete-item :key="$result" :value="$result" dusk="result-{{ $key }}">
                                            {{ $result }}
                                        </x-autocomplete-item>
                                    @endforeach
                                </x-autocomplete-list>
                            </x-autocomplete>
                        </div>

                        <div dusk="result-output">{{ $selected }}</div>

                        <div
                            x-data="{ selected: null, addNew: null }"
                            dusk="alpine-output"
                            x-on:item-selected.window="selected = $event.detail"
                            x-on:item-add-new.window="addNew = $event.detail"
                            x-on:item-cleared.window="selected = null; input = null"
                            >
                            <div>
                                Alpine Selected: <span dusk="alpine-selected" x-text="selected"></span>
                            </div>

                            <div>
                                Alpine Add New: <span dusk="alpine-add-new" x-text="addNew"></span>
                            </div>
                            <button dusk="alpine-clear" x-on:click="$dispatch('item-clear')">Alpine Clear</button>
                        </div>
                    </div>
                HTML;
            }
        };
    }

    /** @test */
    public function event_is_dispatched_on_selected_item()
    {
        Livewire::visit($this->component())
            ->assertSeeNothingIn('@alpine-selected')
            ->click('@input')
            ->waitForLivewire()->click('@result-1')
            ->assertSeeIn('@alpine-selected', 'john');
    }

    /** @test */
    public function event_is_dispatched_on_add_new_item()
    {
        Livewire::visit($this->component())
            ->assertSeeNothingIn('@alpine-add-new')
            ->click('@input')
            ->waitForLivewire()->type('@input', 'bbb')
            ->click('@new-item')
            ->assertSeeIn('@alpine-add-new', 'bbb');
    }

    /** @test */
    public function event_is_dispatched_on_selected_item_and_input_is_changed()
    {
        Livewire::visit($this->component())
            ->assertSeeNothingIn('@alpine-selected')
            ->click('@input')
            ->waitForLivewire()->type('@input', 'b')
            ->waitForLivewire()->click('@result-1')
            ->assertSeeIn('@alpine-selected', 'bill');
    }

    /** @test */
    public function event_is_dispatched_when_selected_item_is_cleared()
    {
        Livewire::visit($this->component())
            ->click('@input')
            ->waitForLivewire()->click('@result-1')
            ->assertSeeIn('@alpine-selected', 'john')
            ->waitForLivewire()->click('@clear-button')
            ->assertSeeNothingIn('@alpine-selected');
    }

    /** @test */
    public function selected_is_cleared_when_clear_event_received()
    {
        Livewire::visit($this->component())
            ->click('@input')
            ->waitForLivewire()->click('@result-2')
            ->assertSeeIn('@result-output', 'bill')
            ->assertValue('@input', 'bill')
            ->assertSeeIn('@alpine-selected', 'bill')
            ->waitForLivewire()->click('@alpine-clear')
            ->assertSeeNothingIn('@result-output')
            ->assertValue('@input', '')
            ->assertSeeNothingIn('@alpine-selected');
    }
}
