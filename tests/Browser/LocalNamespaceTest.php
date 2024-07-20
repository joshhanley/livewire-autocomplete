<?php

namespace LivewireAutocomplete\Tests\Browser;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class LocalNamespaceTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('livewire-autocomplete.use_global_namespace', false);
    }

    public function component()
    {
        return new class extends Component
        {
            public $input;

            public $selected;

            public function updated()
            {
                usleep(500000); // 0.5 seconds
            }

            #[Computed]
            public function results()
            {
                if ($this->input === null) {
                    return collect();
                }

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
                    <x-lwa::autocomplete wire:model.live="selected" dusk="autocomplete">
                        <x-lwa::autocomplete.input wire:model.live="input" dusk="input">
                            <x-lwa::autocomplete.clear-button dusk="clear-button" />
                        </x-lwa::autocomplete.input>

                        <x-lwa::autocomplete.list dusk="dropdown" x-cloak>
                            <x-lwa::autocomplete.loading dusk="loading" />

                            @if (!$input && $this->results->isEmpty())
                                <x-lwa::autocomplete.prompt dusk="prompt" />
                            @endif

                            @if ($input)
                                <x-lwa::autocomplete.new-item :value="$input" dusk="add-new" />

                                @forelse($this->results as $key => $result)
                                    <x-lwa::autocomplete.item :key="$result" :value="$result" dusk="result-{{ $key }}">
                                        {{ $result }}
                                    </x-lwa::autocomplete.item>
                                @empty
                                    <x-lwa::autocomplete.empty dusk="empty">
                                        No results found
                                    </x-lwa::autocomplete.empty>
                                @endforelse
                            @endif
                        </x-lwa::autocomplete.list>
                    </x-lwa::autocomplete>

                    <div dusk="result-output">{{ $selected }}</div>
                </div>
                HTML;
            }
        };
    }

    /** @test */
    public function all_components_load_successfully_from_local_namespace()
    {
        Livewire::visit($this->component())
            ->waitForLivewireToLoad()
            ->assertVisible('@autocomplete')
            ->assertVisible('@input')
            ->click('@input')
            ->assertVisible('@dropdown')
            ->assertVisible('@prompt')
            ->keys('@input', 'b')
            ->assertVisible('@loading')
            ->waitForLivewire()->keys('@input', 'o')
            ->assertVisible('@add-new')
            ->assertVisible('@result-0')
            ->keys('@input', '{BACKSPACE}')
            ->waitForLivewire()->keys('@input', 'b')
            ->assertVisible('@empty')
            ->waitForLivewire()->keys('@input', '{BACKSPACE}')
            ->keys('@input', '{ARROW_DOWN}')
            ->keys('@input', '{ARROW_DOWN}')
            ->waitForLivewire()->keys('@input', '{ENTER}')
            ->assertVisible('@clear-button');
    }
}
