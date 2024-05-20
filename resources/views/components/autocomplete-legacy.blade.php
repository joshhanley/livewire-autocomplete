@props([
    'name' => 'autocomplete',
    'options' => [],
    'components' => '',
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
    $options = array_merge($defaultOptions, config('livewire-autocomplete.legacy_options', []), $options);
    $getOption = fn($option) => $options[$option] ?? null;
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
@endphp

{{-- @todo: Fix this so it supports namespacing --}}
<x-autocomplete :auto-select="$autoSelect" :wire:model.live="$selectedProperty->value">
    @if($loadOnceOnFocus)
        <x-autocomplete-input
            :wire:model.live="$inputProperty->value"
            :wire:focus.once="$focusAction->value"
            class="bg-white"
            x-bind:disabled="id"
            dusk="autocomplete-input" />
    @else
        <x-autocomplete-input
            :wire:model.live="$inputProperty->value"
            :wire:focus="$focusAction->value"
            class="bg-white"
            x-bind:disabled="id"
            dusk="autocomplete-input" />
    @endif

    {{-- clear-button --}}
    <div x-show="id" class="absolute right-0 inset-y-0 pr-3 flex items-center" x-cloak>
        <button type="button"
            x-on:click="clear"
            class="border-2 border-gray-300 rounded bg-white text-gray-700 transition-transform ease-in-out duration-100 transform hover:scale-105 hover:text-black focus:outline-none focus:border-blue-400"
            dusk="clear">
            <svg class="h-5 w-5 fill-current"
                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z"></path>
            </svg>
        </button>
    </div>

    <x-autocomplete-list class="mx-2 mt-1 max-h-56 overflow-y-auto" dusk="autocomplete-dropdown">
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
                    @if ($allowNew  && strlen($inputValue) > 0)
                        <x-autocomplete-new-item
                            :value="$inputValue"
                            wire:key='{{ $name }}-add-new'
                            :active="$getOption('result-focus-styles')"
                            dusk="add-new">
                            Add new "{{ $inputValue }}"
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
                                dusk="result-{{ $key }}">
                                {{  $result[$getOption('text')] ?? $result }}
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
