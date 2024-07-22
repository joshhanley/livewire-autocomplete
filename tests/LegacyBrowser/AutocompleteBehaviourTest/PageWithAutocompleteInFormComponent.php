<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\AutocompleteBehaviourTest;

use Illuminate\Support\Collection;
use Livewire\Component;

class PageWithAutocompleteInFormComponent extends Component
{
    public $results = [
        'bob',
        'john',
        'bill',
    ];

    public $input = '';

    public $selected;

    public $saved = false;

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

    public function save()
    {
        $this->saved = true;
    }

    public function render()
    {
        return <<<'HTML'
            <div dusk="page">
                <form wire:submit.prevent="save">
                <x-autocomplete.legacy
                    wire:model-text="input"
                    wire:model-id="selected"
                    wire:model-results="results"
                    />
                </form>

                <div dusk="saved-output">{{ var_export($saved, true) }}</div>
                <div dusk="result-output">{{ $selected }}</div>
            </div>
            HTML;
    }
}
