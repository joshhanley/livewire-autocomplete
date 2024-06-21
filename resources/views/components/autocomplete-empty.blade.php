@php($componentNamePrefix = config('livewire-autocomplete.use_global_namespace', false) ? '' : (config('livewire-autocomplete.namespace', 'lwa') . '::'))

<x-dynamic-component :component="$componentNamePrefix . 'autocomplete-item'" {{ $attributes }}>
    {{ $slot->isNotEmpty() ? $slot : 'No results found' }}
</x-dynamic-component>
