<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\AutocompleteBehaviourTest;

use Illuminate\Support\Collection;
use Livewire\Component;

class PageWithPreSelectedValueComponent extends Component
{
    public $results = [
        'bob',
        'john',
        'bill',
    ];

    public $input = 'bob';

    public $selected = 0;

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

    public function changeSelected()
    {
        $this->input = 'john';
        $this->selected = 1;
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
                <x-autocomplete.legacy
                    wire:model-text="input"
                    wire:model-id="selected"
                    wire:model-results="results"
                    />

                <div dusk="result-output">{{ $selected }}</div>

                <button dusk="change-selected" type="button" wire:click="changeSelected">Change Selected</button>
            </div>
            HTML;
    }
}
