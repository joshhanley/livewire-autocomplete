<?php

namespace LivewireAutocomplete\Tests\Browser;

use Livewire\Component;

class PageWithAutocompleteComponent extends Component
{
    public function render()
    {
        return
<<<'HTML'
<div dusk="page">
    <x-lwc::autocomplete />
</div>
HTML;
    }
}
