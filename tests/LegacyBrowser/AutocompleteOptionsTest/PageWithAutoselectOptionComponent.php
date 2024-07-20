<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\AutocompleteOptionsTest;

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
                <p dusk="some-element-other-than-the-input">some-element-other-than-the-input</p>
                <x-autocomplete.legacy
                    wire:model-text="input"
                    wire:model-id="selected"
                    wire:model-results="results"
                    :options="[
                        'auto-select' => $autoselect,
                    ]"
                    />

                <div dusk="result-output">{{ $selected }}</div>
            </div>
            HTML;
    }
}
