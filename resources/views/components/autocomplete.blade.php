@props([
    'unstyled' => false,
    'autoSelect' => false,
])

<div
    x-data="autocomplete({
        id: $wire.entangle('{{ $attributes->wire('model')->value }}'),
        autoSelect: @js($autoSelect),
    })"
    x-on:keydown.escape="escape($event)"
    x-on:click.outside="outside()"
    {{ $attributes->whereDoesntStartWith('wire:model')->class(['' => !$unstyled, 'relative']) }}>
    {{ $slot }}
</div>

@if (config('livewire-autocomplete.inline-scripts'))
    @once
        <script src="{{ route('livewire-autocomplete.asset', 'autocomplete.js') }}"></script>
    @endonce
@endif
