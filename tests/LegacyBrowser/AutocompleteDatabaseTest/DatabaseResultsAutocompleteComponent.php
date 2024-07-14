<?php

namespace LivewireAutocomplete\Tests\LegacyBrowser\AutocompleteDatabaseTest;

use Livewire\Component;
use LivewireAutocomplete\Tests\LegacyBrowser\AutocompleteDatabaseTest\Models\Item;

class DatabaseResultsAutocompleteComponent extends Component
{
    public $items;

    public $itemName = '';

    public $selectedItem;

    public $rules = [
        'items.*.id' => '',
        'items.*.name' => '',
        'selectedItem' => '',
    ];

    public function mount()
    {
        $this->getItems();
    }

    public function getItems()
    {
        $this->items = Item::query()
            ->when($this->itemName, function ($query, $itemName) {
                return $query->where('name', 'LIKE', "%{$itemName}%");
            })
            ->get();
    }

    public function updatedItemName()
    {
        $this->reset('items');
        $this->getItems();
    }

    public function updatedSelectedItem($selected)
    {
        $this->selectedItem = Item::find($selected ?? null);
        $this->itemName = $this->selectedItem->name ?? null;
    }

    public function render()
    {
        return <<<'HTML'
            <div dusk="page">
                <x-autocomplete-legacy
                    wire:model-text="itemName"
                    wire:model-id="selectedItem"
                    wire:model-results="items"
                    :options="[
                        'text' => 'name',
                    ]"
                    search-attribute="name"
                    />

                <div dusk="result-output">@if($selectedItem)ID:{{ $selectedItem->id }} - Name:{{ $selectedItem->name }}@endif</div>
            </div>
            HTML;
    }
}
