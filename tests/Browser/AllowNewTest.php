<?php

namespace LivewireAutocomplete\Tests\Browser;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Livewire;

class AllowNewTest extends BrowserTestCase
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
                    <x-autocomplete wire:model.live="selected">
                        <x-autocomplete-input wire:model.live="input" dusk="input" />

                        <x-autocomplete-list dusk="dropdown" x-cloak>
                            @if($input !== '' && $input !== null)
                                <x-autocomplete-new-item dusk="add-new" />
                            @endif

                            @foreach($this->results as $index => $result)
                                <x-autocomplete-item :key="$result['id']" :value="$result['name']" dusk="result-{{ $result['id'] }}">
                                    {{ $result['name'] }}
                                </x-autocomplete-item>
                            @endforeach
                        </x-autocomplete-list>
                    </x-autocomplete>

                    <div dusk="selected-output">{{ $selected }}</div>
                    <div dusk="input-output">{{ $input }}</div>
                </div>
                HTML;
            }
        };
    }

    /** @test */
    public function add_new_row_is_not_shown_when_there_is_no_input()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->assertNotPresent('@add-new')
        ;
    }

    /** @test */
    public function add_new_row_appears_when_allow_new_is_true_and_text_is_entered()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@input', 'b')
            ->assertPresent('@add-new')
            ->assertSeeIn('@add-new', 'Add new "b"')
        ;
    }

    /** @test */
    public function add_new_row_is_visible_when_no_results_found()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@input', 'greg')
            ->assertPresent('@add-new')
            ->assertSeeIn('@add-new', 'Add new "greg"')
        ;
    }

    /** @test */
    public function first_result_should_be_highlighted_when_add_new_row_not_displayed_yet()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->keys('@input', '{ARROW_DOWN}')
            ->assertHasClass('@result-0', 'bg-blue-500')
        ;
    }

    /** @test */
    public function add_new_row_should_be_highlighed_when_add_new_row_is_displayed()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@input', 'b')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertHasClass('@add-new', 'bg-blue-500')
        ;
    }

    /** @test */
    public function add_new_row_should_be_highlighed_after_arrowing_down_and_back_up()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@input', 'b')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertHasClass('@add-new', 'bg-blue-500')
            ->keys('@input', '{ARROW_DOWN}')
            ->keys('@input', '{ARROW_DOWN}')
            ->assertClassMissing('@add-new', 'bg-blue-500')
            ->assertHasClass('@result-1', 'bg-blue-500')
            ->keys('@input', '{ARROW_UP}')
            ->keys('@input', '{ARROW_UP}')
            ->assertHasClass('@add-new', 'bg-blue-500')
            ->assertClassMissing('@result-1', 'bg-blue-500')
        ;
    }

    /** @test */
    public function first_result_should_be_selected_when_add_new_row_not_displayed_yet()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->keys('@input', '{ARROW_DOWN}')
            ->waitForLivewire()->keys('@input', '{TAB}')
            ->assertSeeIn('@selected-output', '1')
            ->assertSeeIn('@input-output', 'bob')
        ;
    }

    /** @test */
    public function add_new_row_should_be_selected_but_nothing_happen()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@input', 'j')
            ->keys('@input', '{ARROW_DOWN}')
            ->keys('@input', '{TAB}')
            // Pause to allow Livewire to run if it was going to
            ->pause(100)
            ->assertSeeNothingIn('@selected-output')
            ->assertSeeIn('@input-output', 'j')
        ;
    }

    /** @test */
    public function highlighted_record_should_be_selected_even_when_add_new_row_displayed()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@input', 'j')
            ->keys('@input', '{ARROW_DOWN}')
            ->keys('@input', '{ARROW_DOWN}')
            ->waitForLivewire()->keys('@input', '{TAB}')
            ->assertSeeIn('@selected-output', '2')
            ->assertSeeIn('@input-output', 'john')
        ;
    }

    /** @test */
    public function escape_does_not_clear_the_input_when_add_new_allowed()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@input', 'b')
            ->assertPresent('@add-new')
            ->assertValue('@input', 'b')
            ->keys('@input', '{ESCAPE}')
            ->assertValue('@input', 'b')
        ;
    }

    /** @test */
    public function clicking_away_does_not_clear_the_input_when_add_new_allowed()
    {
        Livewire::visit($this->component())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(100)
            ->waitForLivewire()->type('@input', 'c')
            ->assertPresent('@add-new')
            ->assertValue('@input', 'c')
            ->clickAtXPath('//body')
            ->assertValue('@input', 'c')
        ;
    }
}
