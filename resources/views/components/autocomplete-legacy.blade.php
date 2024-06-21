@props([
    'name' => 'autocomplete',
    'options' => [],
    'components' => [],
])

@php
    $defaultOptions = [
        'id' => 'id',
        'text' => 'text',
        'auto-select' => true,
        'allow-new' => true,
        'load-once-on-focus' => true,
        'inline' => false,
        'inline-styles' => 'relative',
        'overlay-styles' => 'absolute z-30',
        'result-focus-styles' => 'bg-blue-500',
    ];

    $defaultComponents = [
        'add-new-row' => 'add-new-row',
        'result-row' => 'result-row',
    ];

    $options = array_merge($defaultOptions, config('livewire-autocomplete.legacy_options', []), $options);
    $components = array_merge($defaultComponents, config('livewire-autocomplete.legacy_components', []), $components);
    $getOption = fn($option) => $options[$option] ?? null;
    $getComponent = fn($component) => $components[$component] ?? null;
    $hasResults = fn($results) => is_countable($results) && count($results) > 0;
    $hasInputText = fn($inputText) => $inputText !== null && $inputText != '';
    $shouldShowPlaceholder = fn($results, $inputText) => !$hasResults($results) && !$hasInputText($inputText);

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

    $addNewRowComponent = $getComponent('add-new-row') !== 'add-new-row' ? $getComponent('add-new-row') : null;
    $resultRowComponent = $getComponent('result-row') !== 'result-row' ? $getComponent('result-row') : null;
@endphp

{{-- @todo: Fix this so it supports namespacing --}}
<x-autocomplete :auto-select="$autoSelect" :wire:model.live="$selectedProperty->value">
    @if ($loadOnceOnFocus)
        <x-autocomplete-input
            :wire:model.live="$inputProperty->value"
            :wire:focus.once="$focusAction->value"
            class="bg-white"
            x-bind:disabled="id"
            dusk="autocomplete-input">
            <x-autocomplete-clear-button />
        </x-autocomplete-input>
    @else
        <x-autocomplete-input
            :wire:model.live="$inputProperty->value"
            :wire:focus="$focusAction->value"
            class="bg-white"
            x-bind:disabled="id"
            dusk="autocomplete-input">
            <x-autocomplete-clear-button />
        </x-autocomplete-input>
    @endif

    <x-autocomplete-list
        :inline="$getOption('inline')"
        :containerClass="$getOption('inline') ? $getOption('inline-styles') : $getOption('overlay-styles')"
        class="mx-2 mt-1 max-h-56 overflow-y-auto"
        dusk="autocomplete-dropdown">
        <div @class('hidden relative w-full py-2 h-10 flex items-center justify-center') wire:loading.flex dusk="autocomplete-loading">
            <div class="absolute inset-0 bg-gray-500 opacity-25"></div>
            <svg class="animate-spin h-4 w-4 text-cool-gray-700 stroke-current" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <div wire:loading.remove>
            @if ($shouldShowPlaceholder($resultsValue, $inputValue))
                {{-- prompt --}}
                <div class="px-3 py-2" wire:key="{{ $name }}-prompt">
                    Start typing to search...
                </div>
            @else
                @if ($hasResults($resultsValue) || $allowNew)
                    @if ($allowNew && strlen($inputValue) > 0)
                        <x-autocomplete-new-item
                            :value="$inputValue"
                            wire:key='{{ $name }}-add-new'
                            :active="$getOption('result-focus-styles')"
                            :unstyled="$addNewRowComponent !== null"
                            dusk="add-new">
                            @if ($addNewRowComponent)
                                <x-dynamic-component :component="$addNewRowComponent" :inputText="$inputValue" />
                            @else
                                Add new "{{ $inputValue }}"
                            @endif
                        </x-autocomplete-new-item>
                    @endif

                    @if ($resultsValue)
                        @foreach ($resultsValue as $key => $result)
                            {{-- @todo: Change these to get from options if available --}}
                            <x-autocomplete-item
                                :key="$result[$getOption('id')] ?? $result"
                                :value="$result[$getOption('text')] ?? $result"
                                wire:key="{{ $name }}-result-{{ $key }}"
                                :active="$getOption('result-focus-styles')"
                                :unstyled="$resultRowComponent !== null"
                                dusk="result-{{ $key }}">
                                @if ($resultRowComponent)
                                    <x-dynamic-component :component="$resultRowComponent" :result="$result" :textAttribute="$getOption('text')" />
                                @else
                                    {{ $result[$getOption('text')] ?? $result }}
                                @endif
                            </x-autocomplete-item>
                        @endforeach
                    @endif
                @else
                    {{-- no-results --}}
                    <div class="px-3 py-2" wire:key="{{ $name }}-no-results">
                        No results found
                    </div>
                @endif
            @endif
        </div>
    </x-autocomplete-list>
</x-autocomplete>
