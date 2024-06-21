<x-autocomplete-item {{ $attributes }}>
    {{ $slot->isNotEmpty() ? $slot : 'Start typing to search' }}
</x-autocomplete-item>
