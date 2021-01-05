@props([
'name' => null,
/** Assign required wire attributes to local properties */
'inputProperty' => $attributes->wire('input-property'),
'resultsProperty' => $attributes->wire('results-property'),
'selectedProperty' => $attributes->wire('selected-property'),
'resultComponent' => null,
'searchAttribute' => null,
])

@php
/** Remove all wire attributes that are assigned to local properties from the attribute bag */
$attributes = $attributes->except(['wire:input-property', 'wire:results-property', 'wire:selected-property'])
@endphp

{{-- @dd(get_defined_vars()) --}}

<div x-data="autocomplete({
    name: {{ json_encode($name) }},
    value: {!!  $inputProperty->value ? " \$wire.entangle('" . $inputProperty . "')" : 'null' !!},
    results: @entangle($resultsProperty),
    selected: {!! $selectedProperty->value ? "\$wire.entangle('" . $selectedProperty . "')" : 'null' !!},
    searchAttribute: {{ "'" . $searchAttribute . "'" ?? 'null' }}
    })" x-init="init($dispatch)" x-on:click.away="close()">
    <div class="relative">
        <input
            x-model.debounce.300ms="value"
            x-on:focus="showDropdown = true"
            x-on:keydown.tab="tab($dispatch)"
            x-on:keydown.shift.window="shift(true)" {{-- Detect shift on window otherwise shift+tab from another field not recognised --}}
            x-on:keyup.shift.window="shift(false)" {{-- Detect shift on window otherwise shift+tab from another field not recognised --}}
            x-on:blur.window="shift(false)" {{-- Clear shift on window blur otherwise can't select --}}
            x-on:keydown.escape.prevent="showDropdown = false; event.target.blur()"
            x-on:keydown.enter.stop.prevent="selectItem($dispatch); event.target.blur()"
            x-on:keydown.arrow-up.prevent="focusPrevious()"
            x-on:keydown.arrow-down.prevent="focusNext()"
            x-on:keydown.home.prevent="focusFirst()"
            x-on:keydown.end.prevent="focusLast()"
            x-on:input.debounce.300ms="input($dispatch)"
            class="w-full pl-4 py-2 rounded border border-cool-gray-200 shadow-inner leading-5 text-cool-gray-900 placeholder-cool-gray-400"
            x-bind:class="[selected ? 'pr-9' : 'pr-4']"
            type="text"
            dusk="autocomplete-input"
            x-bind:disabled="selected"
            {{-- @if ($this->getPropertyValue($selectedProperty)) disabled @endif --}}
        />

        <div x-on:click="clearItem($dispatch)" class="absolute right-0 inset-y-0 flex items-center">
            {{-- @if ($this->getPropertyValue($selectedProperty)) --}}
                <button x-show="selected" type="button" class="group focus:outline-none" dusk="clear" x-cloak>
                    {{-- @if ($clear)
                        {{ $clear }}
                        @else --}}
                        <div class="mr-2">
                            <svg class="h-5 w-5 border-2 rounded group-focus:border-blue-400 text-gray-700 fill-current transition-transform ease-in-out duration-100 transform hover:scale-105 hover:text-black"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z"></path>
                            </svg>
                        </div>
                        {{--
                    @endif --}}
                </button>
                {{--
            @endif --}}
        </div>
    </div>

    <div x-show="showDropdown && hasResults()" x-on:click="selectItem($dispatch)" x-on:mouseleave="focusIndex = null" class="relative" dusk="autocomplete-dropdown" x-cloak>
        <div wire:loading.delay.class.remove="hidden" class="hidden absolute inset-0 flex items-center justify-center" dusk="autocomplete-loading">
            <div class="absolute inset-0 bg-gray-500 opacity-25"></div>
            <svg class="animate-spin h-4 w-4 text-cool-gray-700 stroke-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        @foreach ($this->getPropertyValue($resultsProperty) as $key => $result)
            <div
                wire:key="result-{{ $key }}"
                x-on:mouseenter="focusIndex = {{ $key }}"
                :class="{ 'bg-blue-500' : focusIndex == {{ $key }}}"
                dusk="result-{{ $key }}">
                @if ($resultComponent)
                    <x-dynamic-component :component="$resultComponent" :model="$result" />
                @else
                    <div>
                        {{ $result }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

@once
    <script>
        function autocomplete(config) {
            return {
                showDropdown: false,
                ...config,
                focusIndex: null,
                resultsCount: null,
                shiftIsPressed: false,
                selectOnTab: true,

                init($dispatch) {
                    this.$watch('results', () => this.clearResultsCount())
                },

                show() {
                    this.showDropdown = true
                },

                hide() {
                    this.showDropdown = false
                },

                isShown() {
                    return this.showDropdown
                },

                isHidden() {
                    return !this.isShown()
                },

                tab($dispatch) {
                    if (this.shiftIsPressed) return this.close()

                    if (this.selectOnTab) return this.selectItem($dispatch)

                    return this.close()
                },

                shift(isPressed) {
                    this.shiftIsPressed = isPressed
                },

                close() {
                    if (this.isHidden()) return

                    this.hide()
                    this.clearFocus();
                },

                clearFocus() {
                    this.focusIndex = null
                },

                hasResults() {
                    return this.totalResults() > 0
                },

                hasNoResults() {
                    return !this.hasResults()
                },

                clearResultsCount() {
                    this.resultsCount = null
                },

                totalResults() {
                    if (this.resultsCount) return this.resultsCount //Use memoised count

                    // if (this.isGrouped) {
                    //     return this.resultsCount = this.totalGroupedResults()
                    // }

                    return this.resultsCount = this.results.length
                },

                hasFocus() {
                    return this.focusIndex !== null
                },

                hasNoFocus() {
                    return !this.hasFocus()
                },

                focusIsAtStart() {
                    return this.focusIndex == 0
                },

                focusIsAtEnd() {
                    return this.focusIndex >= this.totalResults() - 1
                },

                focusFirst() {
                    this.focusIndex = 0
                },

                focusLast() {
                    this.focusIndex = this.totalResults() - 1
                },

                focusPrevious() {
                    if (this.hasNoResults()) return this.clearFocus()

                    if (this.hasNoFocus()) return

                    if (this.focusIsAtStart()) return this.clearFocus();

                    this.focusIndex--
                },

                focusNext() {
                    if (this.hasNoResults()) return this.clearFocus()

                    if (this.hasNoFocus()) return this.focusFirst()

                    if (this.focusIsAtEnd()) return

                    this.focusIndex++
                },

                input($dispatch) {
                    this.clearFocus()

                    $dispatch((this.name ?? 'autocomplete') + '-input', this.value)
                },

                selectItem($dispatch) {
                    if (this.hasFocus()) {
                        this.selected = this.results[this.focusIndex]
                        this.value = this.searchAttribute ? this.selected[this.searchAttribute] : this.selected
                        $dispatch((this.name ?? 'autocomplete') + '-selected', this.selected)
                        $dispatch((this.name ?? 'autocomplete') + '-input', this.value)
                    }

                    this.close()
                },

                clearItem($dispatch) {
                    this.selected = null
                    this.value = null
                    $dispatch((this.name ?? 'autocomplete') + '-cleared')
                }
            }
        }

    </script>
@endonce
