<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompletePlaceholderTest;

use Livewire\Component;

class AutocompleteWithPlaceholderComponent extends Component
{
    public $results = [];

    public $input = '';

    public $selected;

    public function render()
    {
        return <<<'HTML'
            <div dusk="page">
                <x-lwc::autocomplete
                    wire:input-property="input"
                    wire:selected-property="selected"
                    wire:results-property="results"
                    results-placeholder="Start typing to search..."
                    />

                <div dusk="result-output">{{ $selected }}</div>
            </div>
            HTML;
    }
}
