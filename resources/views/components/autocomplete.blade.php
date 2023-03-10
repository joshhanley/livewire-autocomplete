@props([
    'unstyled' => false,
])

<div
    x-data="autocomplete({
        key: @entangle($attributes->wire('model')),
    })"
    x-on:click.outside="close()"
    {{ $attributes->whereDoesntStartWith('wire:model')->class([!$unstyled => '']) }}>
    {{ $slot }}
</div>

@if (config('livewire-autocomplete.inline-scripts'))
    @once
        <script src="{{ route('livewire-autocomplete.asset', 'autocomplete.js') }}"></script>
    @endonce
@endif
