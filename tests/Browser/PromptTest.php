<?php

namespace LivewireAutocomplete\Tests\Browser;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class PromptTest extends TestCase
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
                        <x-autocomplete.input wire:model.live="input" dusk="input" />

                        <x-autocomplete.list dusk="dropdown" x-cloak>
                            @if ($input)
                                @forelse($this->results as $index => $result)
                                    <x-autocomplete.item :key="$result['id']" :value="$result['name']" dusk="result-{{ $index }}">
                                        {{ $result['name'] }}
                                    </x-autocomplete.item>
                                @empty
                                    <x-autocomplete.empty dusk="empty" />
                                @endforelse
                            @else
                                <x-autocomplete.prompt dusk="prompt" />
                            @endif
                        </x-autocomplete.list>
                    </x-autocomplete>

                    <div>Selected: <span dusk="selected-output">{{ $selected }}</span></div>
                    <div>Input: <span dusk="input-output">{{ $input }}</span></div>
                </div>
                HTML;
            }
        };
    }

    /** @test */
    public function placeholder_text_prompt_is_shown_on_focus()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(101)
            ->assertVisible('@dropdown')
            ->assertVisible('@prompt')
            ->assertSeeIn('@prompt', 'Start typing to search');
    }

    /** @test */
    public function no_results_text_prompt_is_shown_if_nothing_found()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            // Pause to allow transitions to run
            ->pause(101)
            ->assertVisible('@dropdown')
            ->waitForLivewire()->type('@input', 'a')
            ->assertSeeIn('@dropdown', 'No results found');
    }
}
