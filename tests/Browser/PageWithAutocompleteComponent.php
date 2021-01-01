<?php

namespace LivewireAutocomplete\Tests\Browser;

use Illuminate\Support\Arr;
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
        $this->results = Arr::where($this->results, function ($value) {
            return str_contains($value, $this->input);
        });
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
