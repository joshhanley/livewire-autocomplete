<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompleteLoadingTest;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Component;

class AutocompleteWithLoadingComponent extends Component
{
    public $results = [
        'bob',
        'john',
        'bill'
    ];

    public $input = '';

    public $selected;

    public function calculateResults()
    {
        $this->reset('results');

        $this->results = Collection::wrap($this->results)
            ->filter(function ($result) {
                if (!$this->input) {
                    return true;
                }

                return str_contains($result, $this->input);
            })
            ->values()
            ->toArray();
    }

    public function updatedInput()
    {
        sleep(5);
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
                <x-lwa::autocomplete
                    wire:model-text="input"
                    wire:model-id="selected"
                    wire:model-results="results"
                    />

                <div dusk="result-output">{{ $selected }}</div>
            </div>
            HTML;
    }
}
