```blade
<x-autocomplete-input wire:model.live="input" dusk="input">
    <x-slot:prefix>
        <x-autocomplete-input-prefix class="bg-gray-100 border-r border-gray-300">
            Search
        </x-autocomplete-input-prefix>
    </x-slot:prefix>
    
    <x-autocomplete-clear-button dusk="clear-button" />

    <x-slot:suffix>
        <x-autocomplete-input-suffix class="bg-gray-100 border-l border-gray-300">
            End
        </x-autocomplete-input-suffix>
    </x-slot:suffix>
</x-autocomplete-input>
```

![](attachments/Pasted%20image%2020240621180634.png)