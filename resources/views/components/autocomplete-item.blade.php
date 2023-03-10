@props([
    'unstyled' => false,
    'key',
    'value',
    'active' => 'bg-blue-500',
    'inactive' => 'bg-white',
    'disabled' => 'bg-gray-50 text-gray-500',
    'isDisabled' => false,
])

<li
    wire:autocomplete-key="@js($key)"
    wire:autocomplete-value="@js($value)"
    x-on:mouseenter="focusKey('@js($key)')"
    x-on:mouseleave="resetFocusedKey"
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
    {{ $slot }}
</li>
