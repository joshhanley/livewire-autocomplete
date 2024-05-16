<?php

namespace LivewireAutocomplete\Tests\Browser;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Livewire;

class AutoSelectTest extends BrowserTestCase
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
                    <p dusk="some-element-other-than-the-input">some-element-other-than-the-input</p>
                    <x-autocomplete auto-select wire:model.live="selected">
                        <x-autocomplete-input wire:model.live="input" dusk="input" />

                        <x-autocomplete-list dusk="dropdown" x-cloak>
                            @foreach($this->results as $index => $result)
                                <x-autocomplete-item :key="$result['id']" :value="$result['name']" dusk="result-{{ $index }}">
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

    public function componentWithNewItem()
    {
        return new class extends Component {
            public $input;
            public $selected;

            #[Computed]
            public function results()
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
                    <p dusk="some-element-other-than-the-input">some-element-other-than-the-input</p>
                    <x-autocomplete auto-select wire:model.live="selected">
                        <x-autocomplete-input wire:model.live="input" dusk="input" />

                        <x-autocomplete-list dusk="dropdown" x-cloak>
                            <x-autocomplete-new-item :value="$input" dusk="add-new" />

                            @foreach($this->results as $index => $result)
                                <x-autocomplete-item :key="$result['id']" :value="$result['name']" dusk="result-{{ $index }}">
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
    public function on_autoselect_first_option_is_selected_by_default()
    {
        Livewire::visit($this->component())
            ->click('@input')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
        ;
    }

    /** @test */
    public function on_autoselect_up_arrow_stops_on_first_option()
    {
        Livewire::visit($this->component())
            ->click('@input')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@input', '{ARROW_UP}')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
        ;
    }

    /** @test */
    public function on_autoselect_down_arrow_stops_on_last_option()
    {
        Livewire::visit($this->component())
            ->click('@input')
            ->keys('@input', '{END}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertHasClass('@result-2', 'bg-blue-500')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertHasClass('@result-2', 'bg-blue-500')
        ;
    }

    /** @test */
    public function on_autoselect_mouse_out_does_not_deselect_current_option()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Have to mouseover or mouseleave won't fire
            ->mouseover('@result-0')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')

            // Mousing over the input is outside the dropdown, so the result should remain highlighted
            ->mouseover('@input')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
        ;
    }

    /** @test */
    public function on_autoselect_refocus_first_option_selected()
    {
        Livewire::visit($this->component())
            ->click('@input')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->click('@some-element-other-than-the-input')
            ->click('@input')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
        ;
    }

    /** @test */
    public function on_autoselect_if_no_results_clear_input_on_selection()
    {
        Livewire::visit($this->component())
            ->click('@input')
            ->waitForLivewire()->type('@input', 'steve')
            // Pause to allow transitions to run
            ->pause(100)
            ->assertValue('@input', 'steve')
            ->waitForLivewire()->keys('@input', '{ENTER}')
            ->assertValue('@input', '')
        ;
    }

    /** @test */
    public function on_autoselect_clear_input_on_escape()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@input', 'steve')
            ->assertValue('@input', 'steve')
            ->waitForLivewire()->keys('@input', '{ESCAPE}')
            ->assertValue('@input', '')
        ;
    }

    /** @test */
    public function on_autoselect_escape_does_not_clear_selected_text()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->click('@result-0')
            ->assertValue('@input', 'bob')
            ->keys('@input', '{ESCAPE}')
            // Pause to allow a Livewire request to complete if it was going to
            ->pause(300)
            ->assertValue('@input', 'bob')
        ;
    }

    /** @test */
    public function on_autoselect_escape_does_not_clear_input_value_if_new_item_is_present()
    {
        Livewire::visit($this->componentWithNewItem())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@input', 'steve')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertHasClass('@add-new', 'bg-blue-500')
            ->assertValue('@input', 'steve')
            // Pause to allow a Livewire request to complete if it was going to
            ->pause(300)
            ->assertValue('@input', 'steve')
        ;
    }

    /** @test */
    public function on_autoselect_click_away_clears_input_text()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@input', 'steve')
            ->waitForLivewire()->click('@some-element-other-than-the-input')
            ->assertValue('@input', '')
        ;
    }

    /** @test */
    public function on_autoselect_click_away_does_not_clear_selected_text()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->click('@result-0')
            ->assertValue('@input', 'bob')
            ->click('@some-element-other-than-the-input')
            ->assertValue('@input', 'bob')
        ;
    }

    /** @test */
    public function on_autoselect_click_away_does_not_clear_input_value_if_new_item_is_present()
    {
        Livewire::visit($this->componentWithNewItem())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@input', 'steve')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertHasClass('@add-new', 'bg-blue-500')
            ->assertValue('@input', 'steve')
            ->click('@some-element-other-than-the-input')
            ->assertValue('@input', 'steve')
        ;
    }
}
