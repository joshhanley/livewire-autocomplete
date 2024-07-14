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
    $componentNamePrefix = config('livewire-autocomplete.use_global_namespace', false) ? '' : (config('livewire-autocomplete.namespace', 'lwa') . '::');
@endphp

<x-dynamic-component :component="$componentNamePrefix . 'autocomplete'" :auto-select="$autoSelect" :wire:model.live="$selectedProperty->value">
    @if ($loadOnceOnFocus)
        <x-dynamic-component :component="$componentNamePrefix . 'autocomplete-input'"
            :wire:model.live="$inputProperty->value"
            :wire:focus.once="$focusAction->value"
            class="bg-white"
            x-bind:disabled="id"
            dusk="autocomplete-input">
            <x-dynamic-component :component="$componentNamePrefix . 'autocomplete-clear-button'" dusk="clear" />
        </x-dynamic-component>
    @else
        <x-dynamic-component :component="$componentNamePrefix . 'autocomplete-input'"
            :wire:model.live="$inputProperty->value"
            :wire:focus="$focusAction->value"
            class="bg-white"
            x-bind:disabled="id"
            dusk="autocomplete-input">
            <x-dynamic-component :component="$componentNamePrefix . 'autocomplete-clear-button'" dusk="clear" />
        </x-dynamic-component>
    @endif

    <x-dynamic-component :component="$componentNamePrefix . 'autocomplete-list'"
        :inline="$getOption('inline')"
        :containerClass="$getOption('inline') ? $getOption('inline-styles') : $getOption('overlay-styles')"
        class="mx-2 mt-1 max-h-56 overflow-y-auto"
        dusk="autocomplete-dropdown">
        <x-dynamic-component :component="$componentNamePrefix . 'autocomplete-loading'" dusk="loading" />

        @if ($shouldShowPlaceholder($resultsValue, $inputValue))
            {{-- prompt --}}
            <x-dynamic-component :component="$componentNamePrefix . 'autocomplete-prompt'" wire:key="{{ $name }}-prompt">
                Start typing to search...
            </x-dynamic-component>
        @else
            @if ($hasResults($resultsValue) || $allowNew)
                @if ($allowNew && strlen($inputValue) > 0)
                    <x-dynamic-component :component="$componentNamePrefix . 'autocomplete-new-item'"
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
                    </x-dynamic-component>
                @endif

                @if ($resultsValue)
                    @foreach ($resultsValue as $key => $result)
                        <x-dynamic-component :component="$componentNamePrefix . 'autocomplete-item'"
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
                        </x-dynamic-component>
                    @endforeach
                @endif
            @else
                {{-- no-results --}}
                <x-dynamic-component :component="$componentNamePrefix . 'autocomplete-empty'" wire:key="{{ $name }}-no-results">
                    No results found
                </x-dynamic-component>
            @endif
        @endif
    </x-dynamic-component>
</x-dynamic-component>
