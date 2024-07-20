<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\DynamicComponentsTest;

use Livewire\Component;

class DynamicResultRowComponent extends Component
{
    public $results = [
        [
            'id' => '1',
            'text' => 'bob',
        ],
        [
            'id' => '2',
            'text' => 'john',
        ],
        [
            'id' => '3',
            'text' => 'bill',
        ],
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
                        'allow-new' => true,
                    ]"
                    :components="[
                        'result-row' => 'legacy-custom-row',
                    ]"
                    />

                <div dusk="input-text-output">{{ $inputText }}</div>
                <div dusk="selected-slug-output">{{ $selectedSlug }}</div>
            </div>
            HTML;
    }
}
