<?php

namespace LivewireAutocomplete\Tests\Browser;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Livewire;

class LoadingTest extends BrowserTestCase
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
                sleep(1);
            }

            public function render()
            {
                return <<< 'HTML'
                <div>
                    <x-autocomplete wire:model.live="selected">
                        <x-autocomplete-input wire:model.live="input" dusk="input" />

                        <x-autocomplete-list dusk="dropdown" x-cloak>
                            <x-autocomplete-loading wire:target="input" wire:key="loading" dusk="loading"/>

                            @foreach($this->results as $key => $result)
                                <x-autocomplete-item wire:target="input" wire:loading.remove :key="$result" :value="$result" dusk="result-{{ $key }}">
                                    {{ $result }}
                                </x-autocomplete-item>
                            @endforeach
                        </x-autocomplete-list>
                    </x-autocomplete>

                    <div dusk="result-output">{{ $selected }}</div>
                </div>
                HTML;
            }
        };
    }

    /** @test */
    public function loading_indicator_appears_when_request_is_taking_too_long()
    {
        Livewire::visit($this->component())
            ->assertMissing('@dropdown')
            ->click('@input')
            ->assertMissing('@loading')
            ->type('@input', 'b')
            // Wait for loading indicator to show up
            ->pause(700)
            ->assertVisible('@loading')
            ->assertMissing('@result-0')
        ;
    }
}
