<x-autocomplete-item {{ $attributes }}>
    {{ $slot->isNotEmpty() ? $slot : 'No results found' }}
</x-autocomplete-item>
