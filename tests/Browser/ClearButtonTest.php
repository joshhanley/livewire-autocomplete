<?php

namespace LivewireAutocomplete\Tests\Browser;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class ClearButtonTest extends TestCase
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
                    <x-autocomplete wire:model.live="selected">
                        <x-autocomplete.input wire:model.live="input" dusk="input">
                            <x-autocomplete.clear-button dusk="clear-button" />
                        </x-autocomplete.input>

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

    /** @test */
    public function clear_button_is_shown_on_load()
    {
        Livewire::visit($this->defaultComponent())
            ->click('@input')
            ->waitForLivewire()->click('@result-1')
            ->assertValue('@input', 'john')
            ->assertSeeIn('@result-output', 'john')
            ->click('@input')
            ->waitForLivewire()->click('@clear-button')
            ->assertValue('@input', '')
            ->assertSeeNothingIn('@result-output');
    }
}
