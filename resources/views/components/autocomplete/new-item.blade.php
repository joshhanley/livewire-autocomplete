@props([
    'show' => null,
    'value',
])

@php($componentNamePrefix = config('livewire-autocomplete.use_global_namespace', false) ? '' : (config('livewire-autocomplete.namespace', 'lwa') . '::'))

<x-dynamic-component :component="$componentNamePrefix . 'autocomplete.item'" :show="$show ?? $value" :$value {{ $attributes }}>
    Add new "{{ $value }}"
</x-dynamic-component>