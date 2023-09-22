@php
$inputProperty = $attributes->wire('model-text');
$resultsProperty = $attributes->wire('model-results');
$selectedProperty = $attributes->wire('model-id');
$focusAction = $attributes->wire('focus');

/** Remove all wire attributes that are assigned to local properties from the attribute bag */
$attributes = $attributes->whereDoesntStartWith('wire:');

$inputValue = $this->getPropertyValue($inputProperty->value);
$resultsValue = $this->getPropertyValue($resultsProperty->value);

$autoSelect = filter_var($getOption('auto-select'), FILTER_VALIDATE_BOOLEAN);
$allowNew = filter_var($getOption('allow-new'), FILTER_VALIDATE_BOOLEAN);
$loadOnceOnFocus = filter_var($getOption('load-once-on-focus'), FILTER_VALIDATE_BOOLEAN);
$inline = filter_var($getOption('inline'), FILTER_VALIDATE_BOOLEAN);
@endphp

<x-dynamic-component
    :component="$getComponent('outer-container')"
    x-data="autocomplete({
        name: '{{ $name }}',
        value: $wire.$entangle('{{ $inputProperty->value }}', true),
        decoupledValue: null,
        focusAction: '{{ $focusAction->value ?? null }}',
        results: $wire.$entangle('{{ $resultsProperty->value }}', true),
        selected: $wire.$entangle('{{ $selectedProperty->value }}', true),
        idAttribute: '{{ $getOption('id') }}',
        searchAttribute: '{{ $getOption('text') }}',
        autoSelect: {{ $autoSelect ? 'true' : 'false' }},
        allowNew: {{ $allowNew ? 'true' : 'false' }},
        loadOnceOnFocus: {{ $loadOnceOnFocus ? 'true' : 'false' }},
    })"
    x-init="init($dispatch)"
    x-on:click.away="away($dispatch)">
    <x-dynamic-component
        :component="$getComponent('input')"
        name="{{ $name }}"
        {{ $attributes }}
        x-model="decoupledValue"
        x-on:focus="inputFocus()"
        x-on:keydown.tab="tab($dispatch)"
        x-on:keydown.shift.window="shift(true)"
        {{-- Detect shift on window otherwise shift+tab from another field not recognised --}}
        x-on:keyup.shift.window.debounce.300ms="shift(false)"
        {{-- Detect shift on window otherwise shift+tab from another field not recognised --}}
        x-on:blur.window="shift(false)"
        {{-- Clear shift on window blur otherwise can't select --}}
        x-on:keydown.escape.prevent="escape($dispatch); event.target.blur()"
        x-on:keydown.enter.stop="enter($dispatch, event)"
        x-on:keydown.arrow-up.prevent="focusPrevious()"
        x-on:keydown.arrow-down.prevent="focusNext()"
        x-on:keydown.home.prevent="focusFirst()"
        x-on:keydown.end.prevent="focusLast()"
        x-on:input.debounce.300ms="input($dispatch)"
        x-spread="inputListeners()"
        x-bind="inputListeners()"
        x-ref="input"
        dusk="autocomplete-input" />

    <x-dynamic-component
        :component="$getComponent('clear-button')"
        x-show="selected"
        x-on:click="clearItem($dispatch)"
        dusk="clear" />

    <x-dynamic-component
        :component="$getComponent('dropdown')"
        :class="$inline ? $getOption('inline-styles') : $getOption('overlay-styles')"
        x-show="shouldShow()"
        x-on:mouseleave="mouseLeave()"
        dusk="autocomplete-dropdown">
        <x-dynamic-component :component="$getComponent('loading')" dusk="autocomplete-loading" />

        <x-dynamic-component :component="$getComponent('results-container')">
            @if ($shouldShowPlaceholder($resultsValue, $inputValue))
                <x-dynamic-component :component="$getComponent('prompt')" wire:key="{{ $name }}-prompt" />
            @else
                @if ($hasResults($resultsValue) || $allowNew)
                    <x-dynamic-component :component="$getComponent('results-list')"
                        wire:key="{{ $name }}-results-list"
                        x-on:click.stop="selectItem($dispatch)">
                        @if ($allowNew && strlen($inputValue) > 0)
                            <x-dynamic-component
                                :component="$getComponent('add-new-row')"
                                :input-text="$inputValue"
                                wire:key='{{ $name }}-add-new'
                                x-on:mouseenter="focusIndex = 0"
                                x-bind:class="{ '{{ $getOption('result-focus-styles') }}' : focusIndex == 0 }"
                                x-ref="add-new"
                                dusk="add-new" />
                        @endif

                        @if ($resultsValue)
                            @foreach ($resultsValue as $key => $result)
                                <x-dynamic-component
                                    :component="$getComponent('result-row')"
                                    :search="$inputValue"
                                    :result="$result"
                                    text-attribute="{{ $getOption('text') }}"
                                    wire:key="{{ $name }}-result-{{ $key }}"
                                    x-on:mouseenter="focusIndex = {{ $allowNew && strlen($inputValue) > 0 ? $key + 1 : $key }}"
                                    x-bind:class="{ '{{ $getOption('result-focus-styles') }}' : focusIndex == {{ $allowNew && strlen($inputValue) > 0 ? $key + 1 : $key }} }"
                                    x-ref="result-{{ $key }}"
                                    dusk="result-{{ $key }}" />
                            @endforeach
                        @endif
                    </x-dynamic-component>
                @else
                    <x-dynamic-component :component="$getComponent('no-results')" wire:key='{{ $name }}-no-results' />
                @endif
            @endif
        </x-dynamic-component>
    </x-dynamic-component>
</x-dynamic-component>

@once
    @if (config('autocomplete.inline-scripts', true))
        <script src="{{ route('livewire-autocomplete.asset', 'autocomplete.js') }}"></script>
    @endif
@endonce
