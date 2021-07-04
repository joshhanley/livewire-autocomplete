<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompleteOptionsTest;

use Illuminate\Support\Collection;
use Livewire\Component;

class PageWithAutoselectOptionComponent extends Component
{
    public $results = [
        'bob',
        'john',
        'bill',
    ];

    public $input = '';

    public $selected;

    public $autoselect = false;

    protected $queryString = ['autoselect' => ['except' => false]];

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
            ->toArray()
        ;
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
                    wire:model-text="input"
                    wire:model-id="selected"
                    wire:model-results="results"
                    :options="[
                        'auto_select' => $autoselect,
                    ]"
                    />

                <div dusk="result-output">{{ $selected }}</div>
            </div>
            HTML;
    }
}
