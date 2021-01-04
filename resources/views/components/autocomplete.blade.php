@props([
/** Assign required wire attributes to local properties */
'inputProperty' => $attributes->wire('input-property'),
'resultsProperty' => $attributes->wire('results-property'),
'selectedProperty' => $attributes->wire('selected-property'),
'resultComponent' => null,
])

@php
/** Remove all wire attributes that are assigned to local properties from the attribute bag */
$attributes = $attributes->except(['wire:input-property', 'wire:results-property', 'wire:selected-property'])
@endphp

<div x-data="autocomplete({
    value: @entangle($inputProperty),
    results: @entangle($resultsProperty),
    selected: @entangle($selectedProperty),
})" x-init="init()" x-on:click.away="close()">
    <div class="relative">
        <input
            x-model.debounce.300ms="value"
            x-on:focus="showDropdown = true"
            x-on:keydown.tab="tab()"
            x-on:keydown.shift.window="shift(true)" {{-- Detect shift on window otherwise shift+tab from another field not recognised --}}
            x-on:keyup.shift.window="shift(false)" {{-- Detect shift on window otherwise shift+tab from another field not recognised --}}
            x-on:blur.window="shift(false)" {{-- Clear shift on window blur otherwise can't select --}}
            x-on:keydown.escape.prevent="showDropdown = false; event.target.blur()"
            x-on:keydown.enter.stop.prevent="selectItem(); event.target.blur()"
            x-on:keydown.arrow-up.prevent="focusPrevious()"
            x-on:keydown.arrow-down.prevent="focusNext()"
            x-on:keydown.home.prevent="focusFirst()"
            x-on:keydown.end.prevent="focusLast()"
            x-on:input.debounce.300ms="clearFocus()"
            class="w-full px-4 py-2 rounded border border-cool-gray-200 shadow-inner leading-5 text-cool-gray-900 placeholder-cool-gray-400"
            type="text"
            dusk="autocomplete-input"
            @if ($this->getPropertyValue($selectedProperty)) disabled @endif
        />

        <div x-on:click="clearItem()" class="absolute right-0 inset-y-0 flex items-center">
            @if ($this->getPropertyValue($selectedProperty))
                <button type="button" class="group focus:outline-none" dusk="clear">
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
            @endif
        </div>
    </div>

    <div x-show="showDropdown && hasResults()" x-on:click="selectItem()" x-on:mouseleave="focusIndex = null" dusk="autocomplete-dropdown" x-cloak>
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

                init() {
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

                tab() {
                    if (this.shiftIsPressed) return this.close()

                    if (this.selectOnTab) return this.selectItem()

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

                selectItem() {
                    if (this.hasFocus()) this.selected = this.results[this.focusIndex]

                    this.close()
                },

                clearItem() {
                    this.selected = null;
                }
            }
        }

    </script>
@endonce
