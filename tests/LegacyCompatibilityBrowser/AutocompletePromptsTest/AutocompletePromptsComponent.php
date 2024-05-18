<?php

namespace LivewireAutocomplete\Tests\LegacyCompatibilityBrowser\AutocompletePromptsTest;

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
