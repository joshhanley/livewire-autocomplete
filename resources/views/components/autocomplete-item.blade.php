@props([
    'unstyled' => false,
    'key',
    'id' => null,
    'value' => null,
    'active' => 'bg-blue-500',
    'inactive' => 'bg-white dark:bg-zinc-900',
    'disabled' => 'bg-gray-50 dark:bg-zinc-700 text-gray-500',
    'isDisabled' => false,
    'show',
])

@php
    if (!isset($key) || is_null($key)) {
        if (is_null($value)) {
            $key = '_x_autocomplete_empty';
        } else {
            $key = '_x_autocomplete_new';
        }
    }

    if (array_key_exists('show', get_defined_vars())) {
        $show = (bool) $show;
    } else {
        $show = true;
    }
@endphp

@if ($show)
    <li
        wire:autocomplete-key="@js($key)"
        wire:autocomplete-id="@js($id ?? $key)"
        wire:autocomplete-value="@js($value)"
        x-on:click="selectItem()"
        x-on:mouseenter="focusKey(@js($key))"
        x-on:mouseleave="resetFocusedIndex()"
        x-bind:class="{
            '{{ $isDisabled ? $disabled : $inactive }}': focusedIndexKey() != @js($key),
            '{{ $active }}': focusedIndexKey() == @js($key),
        }"
        {{ $attributes->class([
            'px-3 py-1 dark:text-zinc-100' => !$unstyled,
            'cursor-pointer' => !$unstyled && !$isDisabled,
            $disabled => $isDisabled,
            $inactive => !$isDisabled,
        ]) }}
        {{ $isDisabled ? 'wire:autocomplete-disabled' : '' }}>
        {{ $slot }}
    </li>
@endif
