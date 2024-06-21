@props([
    'show' => null,
    'value',
])

<x-autocomplete-item :show="$show ?? $value" :$value {{ $attributes }}>
    Add new "{{ $value }}"
</x-autocomplete-item>