@props([
    'resultsProperty',
])
<div x-data="autocomplete()" x-on:click.away="open = false">
    <input
        x-model="value"
        x-on:focus="open = true"
        x-on:keydown.escape.prevent="open = false; event.target.blur()"
        x-on:keydown.arrow-up.prevent="focusPrevious()"
        x-on:keydown.arrow-down.prevent="focusNext()"
        x-on:keydown.home.prevent="focusFirst()"
        x-on:keydown.end.prevent="focusLast()"
        class="w-full px-4 py-2 rounded border border-cool-gray-200 shadow-inner leading-5 text-cool-gray-900 placeholder-cool-gray-400"
        type="text"
        dusk="autocomplete-input"
    />

    <div x-show="open" dusk="autocomplete-dropdown" x-cloak>
        @foreach($this->$resultsProperty as $key => $result)
            <div
                :class="{ 'bg-blue-500' : focusIndex == {{ $key }}}"
                dusk="result-{{ $key }}"
            >
                {{ $result }}
            </div>
        @endforeach
    </div>
</div>

@once
<script>
    function autocomplete() {
        return {
            open: false,
            value: @entangle($attributes->wire('model')),
            results: @entangle($resultsProperty),
            focusIndex: null,
            resultsCount: null,

            clearFocus() {
                this.focusIndex = null
            },

            hasResults() {
                return this.totalResults() > 0
            },

            hasNoResults() {
                return ! this.hasResults()
            },

            clearResultsCount() {
                this.resultsCount = null
            },

            totalResults() {
                if(this.resultsCount) return this.resultsCount //Use memoised count

                // if (this.isGrouped) {
                //     return this.resultsCount = this.totalGroupedResults()
                // }

                return this.resultsCount = this.results.length
            },

            hasFocus() {
                return this.focusIndex !== null
            },

            hasNoFocus() {
                return ! this.hasFocus()
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
                if(this.hasNoResults()) return this.clearFocus()

                if(this.hasNoFocus()) return

                if(this.focusIsAtStart()) return this.clearFocus();

                this.focusIndex--
            },

            focusNext() {
                if(this.hasNoResults()) return this.clearFocus()

                if(this.hasNoFocus()) return this.focusFirst()

                if(this.focusIsAtEnd()) return

                this.focusIndex++
            },
        }
    }
</script>
@endonce
