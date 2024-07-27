@props([
    'unstyled' => false,
    'autoSelect' => false,
    'disableEvents' => false,
    'name' => 'autocomplete',
])

<div
    x-data="autocomplete({
        id: $wire.entangle('{{ $attributes->wire('model')->value }}', @js($attributes->wire('model')->hasModifier('live'))),
        autoSelect: @js($autoSelect),
        name: @js($name),
        fireEvents: @js(!$disableEvents),
    })"
    x-on:{{ $name }}-clear.window="fireEvents && clear()"
    x-on:keydown.escape.stop="escape($event)"
    x-on:click.outside="outside()"
    {{ $attributes->whereDoesntStartWith('wire:model')->class(['' => !$unstyled, 'relative']) }}>
    {{ $slot }}
</div>

@if (config('livewire-autocomplete.inline_scripts'))
    @assets
        <script src="{{ route('livewire-autocomplete.asset', 'autocomplete.js') }}"></script>
    @endassets
@endif
