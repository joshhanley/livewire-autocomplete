<?php

namespace LivewireAutocomplete\Tests\Browser\AutocompletePromptsTest;

use Livewire\Component;

class AutocompletePromptsComponent extends Component
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
                    />

                <div dusk="result-output">{{ $selected }}</div>
            </div>
            HTML;
    }
}
