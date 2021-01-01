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

    public function select($index)
    {
        $this->selected = $this->results[$index] ?? null;
        $this->input = $this->selected;

        $this->calculateResults();
    }

    public function calculateResults()
    {
        $this->results = Collection::wrap($this->results)
            ->filter(function ($result) {
                return str_contains($result, $this->input);
            })
            ->values()
            ->toArray();
    }

    public function updatedInput()
    {
        $this->reset('results');
        $this->calculateResults();
    }

    public function render()
    {
        return <<<'HTML'
            <div dusk="page">
                <x-lwc::autocomplete wire:model="input" select-action="select" results-property="results" />

                <div dusk="result-output">{{ $selected }}</div>
            </div>
            HTML;
    }
}
