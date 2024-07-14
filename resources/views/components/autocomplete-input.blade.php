@props([
    'unstyled' => false,
    'containerClass' => '',
])

<div
    x-data="{
        inputValue: $wire.entangle('{{ $attributes->wire('model')->value }}', @js($attributes->wire('model')->hasModifier('live'))),
        wasJustFocused: false,
    }"
    x-init="valueProperty = @js((string) $attributes->wire('model'));
    
    {{-- `inputValue` will be overwritten by `value` once the `x-modelable` directive is applied, so set value first --}}
    value = inputValue"
    x-modelable="inputValue"
    x-model="value"
    {{-- Shift tab must go before tab to ensure it fires first and flags can be set to disable tab --}}
    x-on:keydown.shift.tab="shiftTab()"
    x-on:keydown.tab="tab()"
    x-on:keydown.backspace="clearSelectedItem()"
    x-on:keydown.arrow-up.prevent="focusPrevious()"
    x-on:keydown.arrow-down.prevent="focusNext()"
    x-on:keydown.meta.arrow-up.prevent.stop="focusFirst()"
    x-on:keydown.meta.arrow-down.prevent.stop="focusLast()"
    x-on:keydown.home.prevent="focusFirst()"
    x-on:keydown.end.prevent="focusLast()"
    x-on:keydown.enter.stop="enter($event)"
    @class([$containerClass, 'flex items-center px-3 py-2 gap-x-2 border border-gray-300 dark:border-zinc-600 rounded overflow-hidden bg-white dark:bg-zinc-900 focus-within:border-blue-500 dark:focus-within:border-blue-600 has-[input:disabled]:bg-gray-100 dark:has-[input:disabled]:bg-zinc-700' => !$unstyled])>
    {{ $prefix ?? null }}
    <input
        type="text"
        x-model="inputValue"
        x-on:focus="inputFocus(); wasJustFocused = true"
        x-on:blur="wasJustFocused = false"
        {{ $attributes->whereDoesntStartWith(['wire:model'])->class(['w-full focus:outline-none bg-white dark:bg-zinc-900 text-black dark:text-zinc-100 disabled:bg-zinc-100 dark:disabled:bg-zinc-700' => !$unstyled]) }} />
    {{ $slot }}

    {{ $suffix ?? null}}
</div>
