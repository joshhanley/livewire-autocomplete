<?php

namespace LivewireAutocomplete\Tests\LegacyCompatibilityBrowser\AllowNewTest;

use Illuminate\Support\Collection;
use Livewire\Component;

class AddNewItemComponent extends Component
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

    public function calculateResults()
    {
        $this->reset('results');

        $this->results = Collection::wrap($this->results)
            ->filter(function ($result) {
                if (! $this->inputText) {
                    return true;
                }

                return str_contains($result['text'], $this->inputText);
            })
            ->values()
            ->toArray();
    }

    public function updatedInputText()
    {
        $this->calculateResults();
    }

    public function render()
    {
        return <<<'HTML'
            <div dusk="page">
                <x-autocomplete-legacy
                    wire:model-text="inputText"
                    wire:model-id="selectedSlug"
                    wire:model-results="results"
                    :options="[
                        'auto-select' => true,
                        'allow-new' => true,
                    ]"
                    />

                <div dusk="input-text-output">{{ $inputText }}</div>
                <div dusk="selected-slug-output">{{ $selectedSlug }}</div>
            </div>
            HTML;
    }
}
