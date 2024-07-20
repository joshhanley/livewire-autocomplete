<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\ScrollIntoViewTest;

use Livewire\Component;

class ScrollIntoViewTestComponent extends Component
{
    public $results = [
        'sample1',
        'sample2',
        'sample3',
        'sample4',
        'sample5',
        'sample6',
        'sample7',
        'sample8',
        'sample9',
        'sample10',
        'sample11',
        'sample12',
        'sample13',
    ];

    public $inputText = '';

    public $selectedSlug;

    public function render()
    {
        return <<<'HTML'
            <div dusk="page">
                <x-autocomplete.legacy
                    wire:model-text="inputText"
                    wire:model-id="selectedSlug"
                    wire:model-results="results"
                    :options="[
                        'auto-select' => true,
                    ]"
                    />

                <div dusk="input-text-output">{{ $inputText }}</div>
                <div dusk="selected-slug-output">{{ $selectedSlug }}</div>
            </div>
            HTML;
    }
}
