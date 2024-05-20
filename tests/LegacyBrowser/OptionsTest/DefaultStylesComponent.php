<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\OptionsTest;

use Livewire\Component;

class DefaultStylesComponent extends Component
{
    protected $queryString = [
        'inline',
    ];

    public $inline = false;

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
                <x-autocomplete-legacy
                    wire:model-text="inputText"
                    wire:model-id="selectedSlug"
                    wire:model-results="results"
                    :options="[
                        'inline' => $inline,
                    ]"
                    />

                <div dusk="input-text-output">{{ $inputText }}</div>
                <div dusk="selected-slug-output">{{ $selectedSlug }}</div>
            </div>
            HTML;
    }
}
