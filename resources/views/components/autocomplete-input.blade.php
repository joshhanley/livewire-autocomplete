@props([
    'unstyled' => false,
])

<div
    x-data="{
        inputValue: @entangle($attributes->wire('model')),
        detachedInput: null,
    }"
    x-init="valueProperty = @js((string) $attributes->wire('model'));
    
    $nextTick(() => detachedInput = inputValue)
    
    $watch('detachedInput', () => {
        inputValue = detachedInput
    })"
    x-modelable="detachedInput"
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
    x-on:keydown.enter.stop="enter($event)">
    <input
        type="text"
        x-model="inputValue"
        x-on:focus="inputFocus()"
        {{ $attributes->whereDoesntStartWith('wire:model')->class([!$unstyled => 'border border-gray-300 rounded w-full px-3 py-2']) }} />
</div>
