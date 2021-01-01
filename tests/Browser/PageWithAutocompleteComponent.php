<?php

namespace LivewireAutocomplete\Tests\Browser;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Livewire\Component;

class PageWithAutocompleteComponent extends Component
{
    public $results = [
        'bob',
        'john',
        'bill'
    ];

    public $input = '';

    public function updatedInput()
    {
        $this->results = Collection::wrap($this->results)
            ->filter(function ($result) {
                return str_contains($result, $this->input);
            })
            ->values()
            ->toArray();
    }

    public function render()
    {
        return
<<<'HTML'
<div dusk="page">
    <x-lwc::autocomplete wire:model="input" resultsProperty="results" />
</div>
HTML;
    }
}
