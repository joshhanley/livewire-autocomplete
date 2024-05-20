<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\OptionsTest;

use Livewire\Component;

class CustomAttributesComponent extends Component
{
    public $results = [
        [
            'slug' => 'A',
            'name' => 'bob',
        ],
        [
            'slug' => 'B',
            'name' => 'john',
        ],
        [
            'slug' => 'C',
            'name' => 'bill',
        ],
    ];

    public $inputText = '';

    public $selectedSlug;

    public function render()
    {
        return <<<'HTML'
            <div dusk="page">
                <x-autocomplete-legacy
                    wire:model-text="inputText"
                    wire:model-id="selectedSlug"
                    wire:model-results="results"
                    :options="[
                        'id' => 'slug',
                        'text' => 'name',
                    ]"
                    />

                <div dusk="input-text-output">{{ $inputText }}</div>
                <div dusk="selected-slug-output">{{ $selectedSlug }}</div>
            </div>
            HTML;
    }
}
