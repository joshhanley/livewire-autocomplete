<?php

namespace LivewireAutocomplete\Tests\Browser;

use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Livewire;
use LivewireAutocomplete\Tests\TestCase;

class LoadOnFocusTest extends TestCase
{
    public function defaultComponent()
    {
        return new class extends Component
        {
            protected $queryString = ['loadOnceOnFocus', 'useParameters'];

            public $loadOnceOnFocus = true;

            public $useParameters = false;

            public $results = [];

            public $inputText = '';

            public $selectedSlug;

            public $calculateResultsCalledCount = 0;

            public $parameter1Value = '';

            public $parameter2Value = '';

            public function calculateResults($parameter1 = null, $parameter2 = null)
            {
                $this->calculateResultsCalledCount++;

                $this->parameter1Value = $parameter1;
                $this->parameter2Value = $parameter2;

                $results = [
                    [
                        'id' => '1',
                        'text' => 'bob',
                    ],
                    [
                        'id' => '2',
                        'text' => 'john',
                    ],
                    [
                        'id' => '3',
                        'text' => 'bill',
                    ],
                ];

                $this->results = Collection::wrap($results)
                    ->filter(function ($result) {
                        if (! $this->inputText) {
                            return true;
                        }

                        return str_contains($result['text'], $this->inputText);
                    })
                    ->values()
                    ->toArray();
            }

            public function render()
            {
                return <<<'HTML'
                    <div dusk="page">
                        <x-autocomplete wire:model.live="selectedSlug">
                            @if ($loadOnceOnFocus)
                                <x-autocomplete.input :wire:focus.once="$useParameters ? 'calculateResults(\'some-parameter\', \'other-parameter\')' : 'calculateResults'" wire:model.live="inputText" dusk="input" />
                            @else
                                <x-autocomplete.input :wire:focus="$useParameters ? 'calculateResults(\'some-parameter\', \'other-parameter\')' : 'calculateResults'" wire:model.live="inputText" dusk="input" />
                            @endif

                            <x-autocomplete.list dusk="dropdown">
                                @foreach($this->results as $index => $result)
                                    <x-autocomplete.item :key="$result['id']" :value="$result['text']" dusk="result-{{ $index }}">
                                        {{ $result['text'] }}
                                    </x-autocomplete.item>
                                @endforeach
                            </x-autocomplete.list>
                        </x-autocomplete>

                        <div dusk="number-times-calculate-called">{{ $calculateResultsCalledCount }}</div>
                        <div dusk="parameter-1-value">{{ $parameter1Value }}</div>
                        <div dusk="parameter-2-value">{{ $parameter2Value }}</div>
                        {{ $useParameters ? 'true' : 'false' }}
                    </div>
                    HTML;
            }
        };
    }

    /** @test */
    public function results_are_not_loaded_initially()
    {
        Livewire::visit($this->defaultComponent())
            ->assertSeeIn('@number-times-calculate-called', 0)
            ->assertDontSeeIn('@dropdown', 'bob')
            ->assertDontSeeIn('@dropdown', 'john')
            ->assertDontSeeIn('@dropdown', 'bill');
    }

    /** @test */
    public function it_loads_results_on_focus_if_action_is_present()
    {
        Livewire::visit($this->defaultComponent())
            ->waitForLivewire()->click('@input')
            ->assertSeeIn('@number-times-calculate-called', 1)
            ->assertSeeIn('@dropdown', 'bob')
            ->assertSeeIn('@dropdown', 'john')
            ->assertSeeIn('@dropdown', 'bill');
    }

    /** @test */
    public function it_only_loads_results_once_if_load_once_on_focus_is_set_to_true()
    {
        Livewire::visit($this->defaultComponent())
            ->waitForLivewire()->click('@input')
            ->assertSeeIn('@number-times-calculate-called', 1)
            ->keys('@input', '{ESCAPE}')
            ->click('@input')
            // Wait for livewire request if it was going to happen (it shouldn't)
            ->pause(100)
            ->assertSeeIn('@number-times-calculate-called', 1);
    }

    /** @test */
    public function it_loads_results_on_every_focus_if_load_once_on_focus_is_set_to_false()
    {
        Livewire::withQueryParams(['loadOnceOnFocus' => false])
            ->visit($this->defaultComponent())
            ->waitForLivewire()->click('@input')
            ->assertSeeIn('@number-times-calculate-called', 1)
            ->keys('@input', '{ESCAPE}')
            ->waitForLivewire()->click('@input')
            ->assertSeeIn('@number-times-calculate-called', 2);
    }

    /** @test */
    public function it_can_call_focus_method_with_parameters()
    {
        Livewire::withQueryParams([
            'loadOnceOnFocus' => false,
            'useParameters' => true,
        ])
            ->visit($this->defaultComponent())
            ->assertSeeNothingIn('@parameter-1-value')
            ->assertSeeNothingIn('@parameter-2-value')
            ->waitForLivewire()->click('@input')
            ->assertSeeIn('@parameter-1-value', 'some-parameter')
            ->assertSeeIn('@parameter-2-value', 'other-parameter');
    }
}
