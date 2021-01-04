<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompleteBehaviourTest;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Component;

class PageWithAutocompleteComponent extends Component
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
                <x-lwc::autocomplete
                    wire:input-property="input"
                    wire:selected-property="selected"
                    wire:results-property="results"
                    />

                <div dusk="result-output">{{ $selected }}</div>
            </div>
            HTML;
    }
}
