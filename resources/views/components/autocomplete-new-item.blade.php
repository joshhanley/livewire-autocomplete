@props([
    'unstyled' => false,
    'active' => 'bg-blue-500',
    'inactive' => 'bg-white',
    'disabled' => 'bg-gray-50 text-gray-500',
    'isDisabled' => false,
])

@php
    $key = '_x_autocomplete_new';
@endphp

<li
    wire:autocomplete-key="@js($key)"
    x-on:click="selectItem()"
    x-on:mouseenter="focusKey(@js($key))"
    x-on:mouseleave="resetFocusedKey()"
    x-bind:class="{
        '{{ $isDisabled ? $disabled : $inactive }}': focusedKey != @js($key),
        '{{ $active }}': focusedKey == @js($key),
    }"
    {{ $attributes->class([
        'px-3 py-1' => !$unstyled,
        'cursor-pointer' => !$unstyled && !$isDisabled,
        $disabled => $isDisabled,
        $inactive => !$isDisabled,
    ]) }}
    {{ $isDisabled ? 'wire:autocomplete-disabled' : '' }}>
    @if (isset($slot) && (string) $slot !== '')
        {{ $slot }}
    @else
        Add new "<span x-text="value"></span>"
    @endif
</li>
