@props([
'selectAction',
'resultsProperty',
])
<div x-data="autocomplete()" x-on:click.away="close()">
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
        dusk="autocomplete-input" />

    <div x-show="showDropdown" x-on:click="selectItem()" x-on:mouseleave="focusIndex = null" dusk="autocomplete-dropdown" x-cloak>
        @foreach ($this->$resultsProperty as $key => $result)
            <div
                wire:key="result-{{ $key }}"
                x-on:mouseenter="focusIndex = {{ $key }}"
                :class="{ 'bg-blue-500' : focusIndex == {{ $key }}}"
                dusk="result-{{ $key }}">
                {{ $result }}
            </div>
        @endforeach
    </div>
</div>

@once
    <script>
        function autocomplete() {
            return {
                showDropdown: false,
                value: @entangle($attributes->wire('model')),
                results: @entangle($resultsProperty),
                selectAction: '{{ $selectAction }}',
                focusIndex: null,
                resultsCount: null,
                shiftIsPressed: false,
                selectOnTab: true,

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
                    if (this.hasFocus()) this.$wire.call(this.selectAction, this.focusIndex, this.key)

                    this.close()
                },
            }
        }

    </script>
@endonce
