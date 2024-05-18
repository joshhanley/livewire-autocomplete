<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\LoadOnFocusTest;

use Illuminate\Support\Collection;
use Livewire\Component;

class LoadOnFocusComponent extends Component
{
    protected $queryString = ['loadOnceOnFocus', 'useParameters'];

    public $loadOnceOnFocus = true;

    public $useParameters = false;

    public $results;

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
                @if($useParameters)
                    <x-autocomplete-legacy
                        wire:model-text="inputText"
                        wire:model-id="selectedSlug"
                        wire:model-results="results"
                        wire:focus="calculateResults('some-parameter', 'other-parameter')"
                        :options="[
                            'auto-select' => false,
                            'load-once-on-focus' => $loadOnceOnFocus,
                        ]"
                        />
                @else
                    <x-autocomplete-legacy
                        wire:model-text="inputText"
                        wire:model-id="selectedSlug"
                        wire:model-results="results"
                        wire:focus="calculateResults"
                        :options="[
                            'auto-select' => false,
                            'load-once-on-focus' => $loadOnceOnFocus,
                        ]"
                        />
                @endif

                <div dusk="number-times-calculate-called">{{ $calculateResultsCalledCount }}</div>
                <div dusk="parameter-1-value">{{ $parameter1Value }}</div>
                <div dusk="parameter-2-value">{{ $parameter2Value }}</div>
                {{ $useParameters ? 'true' : 'false' }}
            </div>
            HTML;
    }
}
