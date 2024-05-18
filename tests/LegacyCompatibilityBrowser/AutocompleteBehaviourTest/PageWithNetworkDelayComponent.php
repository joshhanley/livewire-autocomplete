<?php

namespace LivewireAutocomplete\Tests\LegacyCompatibilityBrowser\AutocompleteBehaviourTest;

use Illuminate\Support\Collection;
use Livewire\Component;

class PageWithNetworkDelayComponent extends Component
{
    public $results = [
        'bob',
        'john',
        'bill',
    ];

    public $input = '';

    public $selected;

    public function calculateResults()
    {
        $this->reset('results');

        $this->results = Collection::wrap($this->results)
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
        usleep(0.8 * 1000000);
    }

    public function updatedInput()
    {
        $this->calculateResults();
    }

    public function updatedSelected()
    {
        $this->input = $this->selected ?? null;

        $this->calculateResults();
    }

    public function render()
    {
        return <<<'HTML'
            <div dusk="page">
                <x-autocomplete-legacy
                    wire:model-text="input"
                    wire:model-id="selected"
                    wire:model-results="results"
                    />

                <div dusk="result-output">{{ $selected }}</div>
            </div>
            HTML;
    }
}
