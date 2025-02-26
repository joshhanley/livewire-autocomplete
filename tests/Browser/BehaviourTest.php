<?php

namespace LivewireAutocomplete\Tests\Browser;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class BehaviourTest extends TestCase
{
    public function defaultComponent()
    {
        return new class extends Component
        {
            public $input;

            public $selected;

            #[Computed]
            public function results()
            {
                return collect([
                    'bob',
                    'john',
                    'bill',
                ])
                    ->filter(function ($result) {
                        if (! $this->input) {
                            return true;
                        }

                        return str_contains($result, $this->input);
                    })
                    ->values()
                    ->toArray();
            }

            public function render()
            {
                return <<< 'HTML'
                <div>
                    <div dusk="forMouseAway">Mouse away</div>
                    <x-autocomplete wire:model.live="selected">
                        <x-autocomplete.input wire:model.live="input" dusk="input" />

                        <x-autocomplete.list dusk="dropdown" x-cloak>
                            @foreach($this->results as $key => $result)
                                <x-autocomplete.item :key="$result" :value="$result" dusk="result-{{ $key }}">
                                    {{ $result }}
                                </x-autocomplete.item>
                            @endforeach
                        </x-autocomplete.list>
                    </x-autocomplete>

                    <div dusk="result-output">{{ $selected }}</div>
                </div>
                HTML;
            }
        };
    }

    public function componentWithNumericKeys()
    {
        return new class extends Component
        {
            public $input;

            public $selected;

            #[Computed]
            public function results()
            {
                return collect([
                    [
                        'id' => 0,
                        'name' => 'bob',
                    ],
                    [
                        'id' => 1,
                        'name' => 'john',
                    ],
                    [
                        'id' => 2,
                        'name' => 'bill',
                    ],
                ])
                    ->filter(function ($result) {
                        if (! $this->input) {
                            return true;
                        }

                        return str_contains($result, $this->input);
                    })
                    ->values()
                    ->toArray();
            }

            public function render()
            {
                return <<< 'HTML'
                <div>
                    <div dusk="forMouseAway"></div>
                    <x-autocomplete wire:model.live="selected">
                        <x-autocomplete.input wire:model.live="input" dusk="input" />

                        <x-autocomplete.list dusk="dropdown" x-cloak>
                            @foreach($this->results as $key => $result)
                                <x-autocomplete.item :key="$result['id']" :value="$result['name']" dusk="result-{{ $key }}">
                                    {{ $result['name'] }}
                                </x-autocomplete.item>
                            @endforeach
                        </x-autocomplete.list>
                    </x-autocomplete>

                    <div dusk="result-output">{{ $selected }}</div>
                </div>
                HTML;
            }
        };
    }

    public function componentInForm()
    {
        return new class extends Component
        {
            public $input;

            public $selected;

            public $saved = false;

            #[Computed]
            public function results()
            {
                return collect([
                    'bob',
                    'john',
                    'bill',
                ])
                    ->filter(function ($result) {
                        if (! $this->input) {
                            return true;
                        }

                        return str_contains($result, $this->input);
                    })
                    ->values()
                    ->toArray();
            }

            public function save()
            {
                $this->saved = true;
            }

            public function render()
            {
                return <<< 'HTML'
                <div>
                    <form wire:submit="save">
                        <x-autocomplete wire:model.live="selected">
                            <x-autocomplete.input wire:model.live="input" dusk="input" />

                            <x-autocomplete.list dusk="dropdown" x-cloak>
                                @foreach($this->results as $key => $result)
                                    <x-autocomplete.item :key="$result" :value="$result" dusk="result-{{ $key }}">
                                        {{ $result }}
                                    </x-autocomplete.item>
                                @endforeach
                            </x-autocomplete.list>
                        </x-autocomplete>
                    </form>

                    <div dusk="saved-output">{{ var_export($saved, true) }}</div>
                    <div dusk="result-output">{{ $selected }}</div>
                </div>
                HTML;
            }
        };
    }

    public function componentWithNetworkDelay()
    {
        return new class extends Component
        {
            public $input;

            public $selected;

            #[Computed]
            public function results()
            {
                return collect([
                    'bob',
                    'john',
                    'bill',
                ])
                    ->filter(function ($result) {
                        if (! $this->input) {
                            return true;
                        }

                        return str_contains($result, $this->input);
                    })
                    ->values()
                    ->toArray();
            }

            public function updated()
            {
                usleep(0.8 * 1000000); // 0.8 of a second delay
            }

            public function render()
            {
                return <<< 'HTML'
                <div>
                    <x-autocomplete wire:model.live="selected">
                        <x-autocomplete.input wire:model.live="input" dusk="input" />

                        <x-autocomplete.list dusk="dropdown" x-cloak>
                            @foreach($this->results as $key => $result)
                                <x-autocomplete.item :key="$result" :value="$result" dusk="result-{{ $key }}">
                                    {{ $result }}
                                </x-autocomplete.item>
                            @endforeach
                        </x-autocomplete.list>
                    </x-autocomplete>

                    <div dusk="result-output">{{ $selected }}</div>
                </div>
                HTML;
            }
        };
    }

    public function componentWithPreSelectedValue()
    {
        return new class extends Component
        {
            public $input = 'bob';

            public $selected = 0;

            #[Computed]
            public function results()
            {
                return collect([
                    'bob',
                    'john',
                    'bill',
                ])
                    ->filter(function ($result) {
                        if (! $this->input) {
                            return true;
                        }

                        return str_contains($result, $this->input);
                    })
                    ->values()
                    ->toArray();
            }

            public function changeSelected()
            {
                $this->input = 'john';
                $this->selected = 1;
            }

            public function render()
            {
                return <<< 'HTML'
                <div>
                    <x-autocomplete wire:model.live="selected">
                        <x-autocomplete.input wire:model.live="input" dusk="input" />

                        <x-autocomplete.list dusk="dropdown" x-cloak>
                            @foreach($this->results as $key => $result)
                                <x-autocomplete.item :key="$result" :value="$result" dusk="result-{{ $key }}">
                                    {{ $result }}
                                </x-autocomplete.item>
                            @endforeach
                        </x-autocomplete.list>
                    </x-autocomplete>

                    <div dusk="result-output">{{ $selected }}</div>

                    <button dusk="change-selected" type="button" wire:click="changeSelected">Change Selected</button>
                </div>
                HTML;
            }
        };
    }

    public function componentWithoutLiveModifiers()
    {
        return new class extends Component
        {
            public $input;

            public $selected;

            #[Computed]
            public function results()
            {
                return collect([
                    'bob',
                    'john',
                    'bill',
                ])
                    ->filter(function ($result) {
                        if (! $this->input) {
                            return true;
                        }

                        return str_contains($result, $this->input);
                    })
                    ->values()
                    ->toArray();
            }

            public function render()
            {
                return <<< 'HTML'
                <div>
                    <div dusk="forMouseAway"></div>
                    <x-autocomplete wire:model="selected">
                        <x-autocomplete.input wire:model="input" dusk="input" />

                        {{-- Don't actually need a search method here, so just calling `$refresh` --}}
                        <button type="button" wire:click="$refresh" dusk="search-button">Search</button>

                        <x-autocomplete.list dusk="dropdown" x-cloak>
                            @foreach($this->results as $key => $result)
                                <x-autocomplete.item :key="$result" :value="$result" dusk="result-{{ $key }}">
                                    {{ $result }}
                                </x-autocomplete.item>
                            @endforeach
                        </x-autocomplete.list>
                    </x-autocomplete>

                    <div dusk="result-output">{{ $selected }}</div>

                    <button type="button" wire:click="$refresh" dusk="refresh-button">Refresh</button>
                </div>
                HTML;
            }
        };
    }

    /** @test */
    public function an_input_is_shown_on_screen()
    {
        Livewire::visit($this->defaultComponent())
            ->assertPresent('@input');
    }

    /** @test */
    public function dropdown_appears_when_input_is_focused()
    {
        Livewire::visit($this->defaultComponent())
            ->assertMissing('@dropdown')
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->assertVisible('@dropdown');
    }

    /** @test */
    public function dropdown_closes_when_anything_else_is_clicked_and_focus_is_removed()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->assertVisible('@dropdown')
            // Click on a different element that's not the autocomplete
            ->click('@forMouseAway')
            // Pause to allow transitions to run
            ->pause(100)
            ->assertNotFocused('@input')
            ->assertMissing('@dropdown');
    }

    /** @test */
    public function dropdown_closes_when_escape_is_pressed_and_focus_removed()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->assertVisible('@dropdown')
            ->keys('@input', '{escape}')
            // Pause to allow transitions to run
            ->pause(100)
            ->assertNotFocused('@input')
            ->assertMissing('@dropdown');
    }

    /** @test */
    public function dropdown_shows_list_of_results()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->assertSeeIn('@dropdown', 'bob')
            ->assertSeeIn('@dropdown', 'john')
            ->assertSeeIn('@dropdown', 'bill')
            ->assertSeeInOrder('@dropdown', ['bob', 'john', 'bill']);
    }

    /** @test */
    public function results_are_filtered_based_on_input()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->assertSeeInOrder('@dropdown', ['bob', 'john', 'bill'])
            ->waitForLivewire()->type('@input', 'b')
            ->assertSeeInOrder('@dropdown', ['bob', 'bill'])
            ->assertDontSeeIn('@dropdown', 'john');
    }

    /** @test */
    public function down_arrow_focus_first_option_if_there_is_no_focus_in_dropdown()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500');
    }

    /** @test */
    public function down_arrow_focus_next_option_if_there_is_already_a_focus_in_dropdown()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->keys('@input', '{ARROW_DOWN}')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertHasClass('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500');
    }

    /** @test */
    public function down_arrow_focus_remains_on_last_result_in_dropdown()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->keys('@input', '{ARROW_DOWN}')
            ->keys('@input', '{ARROW_DOWN}')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertHasClass('@result-2', 'bg-blue-500')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertHasClass('@result-2', 'bg-blue-500');
    }

    /** @test */
    public function up_arrow_clears_focus_if_first_option_is_focused_in_dropdown()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@input', '{ARROW_UP}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500');
    }

    /** @test */
    public function up_arrow_focuses_previous_option_if_there_is_another_option_before_current_in_dropdown()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->keys('@input', '{ARROW_DOWN}')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertHasClass('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@input', '{ARROW_UP}')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500');
    }

    /** @test */
    public function up_arrow_focuses_nothing_if_nothing_currently_focused_in_dropdown()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@input', '{ARROW_UP}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500');
    }

    /** @test */
    public function down_and_up_arrow_focuses_result_with_a_zero_as_the_key()
    {
        Livewire::visit($this->componentWithNumericKeys())
            ->click('@input')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertHasClass('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@input', '{ARROW_UP}')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500');
    }

    /** @test */
    public function home_key_focuses_first_result_in_dropdown()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            // Attempt if none selected
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@input', '{HOME}')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')

            // Attempt if one further down the list is selected
            ->keys('@input', '{ARROW_DOWN}')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertHasClass('@result-2', 'bg-blue-500')
            ->keys('@input', '{HOME}')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500');
    }

    /** @test */
    public function end_key_focuses_last_result_in_dropdown()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            // Attempt if none selected
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@input', '{END}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertHasClass('@result-2', 'bg-blue-500')

            // Attempt if one further up the list is selected
            ->keys('@input', '{ARROW_UP}')
            ->keys('@input', '{ARROW_UP}')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@input', '{END}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertHasClass('@result-2', 'bg-blue-500');
    }

    /** @test */
    public function focus_is_cleared_if_input_changes()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->keys('@input', '{ARROW_DOWN}')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertHasClass('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->waitForLivewire()->type('@input', 'b')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500');
    }

    /** @test */
    public function enter_key_selects_currently_focused_result()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->keys('@input', '{ARROW_DOWN}')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertHasClass('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->waitForLivewire()->keys('@input', '{ENTER}')
            ->assertSeeIn('@result-output', 'john');
    }

    /** @test */
    public function enter_key_only_selects_if_there_is_a_currently_focused_result()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@input', '{ENTER}')
            ->pause(300)
            ->assertSeeNothingIn('@result-output');
    }

    /** @test */
    public function enter_key_does_not_submit_form_if_there_is_a_currently_focused_result()
    {
        Livewire::visit($this->componentInForm())
            ->click('@input')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@input', '{ARROW_DOWN}')
            ->waitForLivewire()->keys('@input', '{ENTER}')
            ->assertSeeIn('@saved-output', 'false');
    }

    /** @test */
    public function dropdown_is_hidden_and_focus_cleared_on_selection()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->waitForLivewire()->keys('@input', '{ENTER}')
            // Pause to allow transitions to run
            ->pause(100)
            ->assertSeeIn('@result-output', 'bob')
            ->assertMissing('@dropdown')
            ->click('@input')
            ->assertClassMissing('@result-0', 'bg-blue-500');
    }

    /** @test */
    public function tab_key_selects_currently_focused_result()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->keys('@input', '{END}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertHasClass('@result-2', 'bg-blue-500')
            ->waitForLivewire()->keys('@input', '{TAB}')
            ->assertSeeIn('@result-output', 'bill');
    }

    /** @test */
    public function tab_key_only_selects_if_there_is_a_currently_focused_result()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->keys('@input', '{END}')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertHasClass('@result-2', 'bg-blue-500')
            ->waitForLivewire()->keys('@input', '{TAB}')
            ->assertSeeIn('@result-output', 'bill');
    }

    /** @test */
    public function shift_tab_does_not_select_currently_focused_result()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->keys('@input', '{HOME}')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->keys('@input', '{SHIFT}', '{TAB}')
            ->pause(300)
            ->assertDontSeeIn('@result-output', 'bob');
    }

    /** @test */
    public function mouse_hover_focuses_result()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->mouseover('@result-1')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertHasClass('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            ->mouseover('@result-2')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertHasClass('@result-2', 'bg-blue-500')
            ->mouseover('@result-0')
            ->assertHasClass('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500');
    }

    /** @test */
    public function mouse_leave_clears_focus_result()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->mouseover('@result-1')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertHasClass('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500')
            // Mouseover over a different element that's not the autocomplete
            ->mouseover('@forMouseAway')
            ->assertClassMissing('@result-0', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
            ->assertClassMissing('@result-2', 'bg-blue-500');
    }

    /** @test */
    public function mouse_click_selects_result()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->waitForLivewire()->click('@result-1')
            ->assertSeeIn('@result-output', 'john');
    }

    /** @test */
    public function selected_result_shown_in_input()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->waitForLivewire()->click('@result-1')
            ->assertValue('@input', 'john');
    }

    /** @test */
    public function mouse_click_only_fires_once_on_newly_generated_morphed_results()
    {
        // This is a bug in livewire/livewire#763, this test triggers it without work around.

        // As of Livewire V3 I think this is no longer relevant but leaving the test here anyway.
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->waitForLivewire()->type('@input', 'john')
            ->assertSeeIn('@dropdown', 'john')
            ->assertDontSeeIn('@dropdown', 'bob')
            ->assertDontSeeIn('@dropdown', 'bill')
            // Need to press keys to trigger input events livewire requires
            ->waitForLivewire()->keys('@input', '{BACKSPACE}', '{BACKSPACE}', '{BACKSPACE}', '{BACKSPACE}')
            ->assertSeeInOrder('@dropdown', ['bob', 'john', 'bill'])
            ->waitForLivewire()->click('@result-2')
            ->assertSeeIn('@result-output', 'bill');
    }

    /** @test */
    public function using_shift_does_not_clear_input()
    {
        // This was a bug with the shift keyup firing before the x-model, so needed to add the same debounce on the keyup event.
        // This is no longer needed as the way shift is handled has been changed but leaving test here anyway.
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->keys('@input', '{SHIFT}', 'b')
            ->assertValue('@input', 'B');
    }

    /** @test */
    public function input_does_not_get_overridden_when_multiple_network_requests_are_sent()
    {
        Livewire::visit($this->componentWithNetworkDelay())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)

            ->type('@input', 'bo')
            ->assertValue('@input', 'bo')

            // Pause to give the "network request" enough time to start
            ->pause(500)

            // Then type some more to trigger another request
            ->type('@input', 'bob')
            ->assertValue('@input', 'bob')

            // Wait for original network request to finish
            ->pause(600)

            // Assert it hasn't overwritten the input value back to "bo"
            ->assertValueIsNot('@input', 'bo')
            ->assertValue('@input', 'bob')

            // Wait for second network request to finish
            ->pause(300)

            // Assert final text value is still "bob"
            ->assertValue('@input', 'bob');
    }

    /** @test */
    public function pre_selected_value_is_shown_in_input()
    {
        Livewire::visit($this->componentWithPreSelectedValue())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)

            ->assertValue('@input', 'bob')
            ->assertSeeIn('@result-output', 0);
    }

    /** @test */
    public function pre_selected_value_can_be_changed_from_other_backend_actions()
    {
        Livewire::visit($this->componentWithPreSelectedValue())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)

            ->assertValue('@input', 'bob')
            ->assertSeeIn('@result-output', 0)
            ->waitForLivewire()->click('@change-selected')
            ->assertValue('@input', 'john')
            ->assertSeeIn('@result-output', 1);
    }

    /** @test */
    public function component_without_wire_model_live_modifiers_still_works()
    {
        Livewire::visit($this->componentWithoutLiveModifiers())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->assertSeeInOrder('@dropdown', ['bob', 'john', 'bill'])
            ->assertSeeNothingIn('@result-output')

            ->waitForNoLivewire()->type('@input', 'b')
            ->assertSeeInOrder('@dropdown', ['bob', 'john', 'bill'])
            ->assertSeeNothingIn('@result-output')

            ->waitForLivewire()->click('@search-button')
            ->assertSeeInOrder('@dropdown', ['bob', 'bill'])
            ->assertSeeNothingIn('@result-output')

            ->waitForNoLivewire()->click('@result-0')
            ->assertValue('@input', 'bob')
            ->assertSeeNothingIn('@result-output')

            ->waitForLivewire()->click('@refresh-button')
            ->assertValue('@input', 'bob')
            ->assertSeeIn('@result-output', 'bob');
    }

    /** @test */
    public function pressing_enter_blurs_the_input_when_dropdown_is_open()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@input', 'j')
            ->keys('@input', '{ENTER}')
            // Pause to allow Livewire to run if it was going to
            ->pause(100)
            ->assertSeeNothingIn('@result-output')
            ->assertNotFocused('@input');
    }
}
