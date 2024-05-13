@props([
    'name' => 'autocomplete',
    'options' => '',
    'components' => '',
])

@php
$getOption = fn ($option) => $options[$option] ?? null;
$hasResults = fn ($results) => is_countable($results) && count($results) > 0;
$hasInputText = fn ($inputText) => $inputText !== null && $inputText != '';
$shouldShowPlaceholder = fn ($results, $inputText) => ! $hasResults($results) && ! $hasInputText($inputText);

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
<x-autocomplete :wire:model.live="$selectedProperty->value">
    <x-autocomplete-input :wire:model.live="$inputProperty->value" class="bg-white" />

    {{-- clear-button --}}
    <div x-show="id" class="absolute right-0 inset-y-0 pr-3 flex items-center" x-cloak>
        <button type="button"
            class="border-2 border-gray-300 rounded bg-white text-gray-700 transition-transform ease-in-out duration-100 transform hover:scale-105 hover:text-black focus:outline-none focus:border-blue-400">
            <svg class="h-5 w-5 fill-current"
                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z"></path>
            </svg>
        </button>
    </div>

    <x-autocomplete-list class="mx-2 mt-1">
        @if ($shouldShowPlaceholder($resultsValue, $inputValue))
            {{-- prompt --}}
            <div class="px-3 py-2" wire:key="{{ $name }}-prompt">
                Start typing to search...
            </div>
        @else
            @if ($hasResults($resultsValue) || $allowNew)
                @if ($allowNew) && strlen($inputValue) > 0)
                    <x-autocomplete-new-item :key="0" :value="$inputValue" wire:key='{{ $name }}-add-new'>
                        Add new "{{ $inputValue }}"
                    </x-autocomplete-new-item>
                @endif

                @if ($resultsValue)
                    @foreach ($resultsValue as $key => $result)
                        {{-- @todo: Change these to get from options if available --}}
                        <x-autocomplete-item :key="$result->id" :value="$result->name" wire:key="{{ $name }}-result-{{ $key }}">
                            {{ $result->name }}
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
    </x-autocomplete-list>
</x-autocomplete>