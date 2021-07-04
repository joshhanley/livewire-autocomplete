<?php

namespace LivewireAutocomplete\Tests\Browser\LoadOnFocusTest;

use Illuminate\Support\Collection;
use Livewire\Component;

class LoadOnFocusComponent extends Component
{
    protected $queryString = ['loadOnceOnFocus'];

    public $loadOnceOnFocus = true;

    public $results;

    public $inputText = '';

    public $selectedSlug;

    public $calculateResultsCalledCount = 0;

    public function calculateResults()
    {
        $this->calculateResultsCalledCount++;

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
            ->toArray()
        ;
    }

    public function render()
    {
        return <<<'HTML'
            <div dusk="page">
                <x-lwa::autocomplete
                    wire:model-text="inputText"
                    wire:model-id="selectedSlug"
                    wire:model-results="results"
                    wire:focus="calculateResults"
                    :options="[
                        'auto_select' => false,
                        'load_once_on_focus' => $loadOnceOnFocus,
                    ]"
                    />

                <div dusk="number-times-calculate-called">{{ $calculateResultsCalledCount }}</div>
            </div>
            HTML;
    }
}
