@props([
    'resultsProperty',
])
<div x-data="autocomplete()" x-on:click.away="open = false">
    <input
        x-model="value"
        x-on:focus="open = true"
        x-on:keydown.escape.prevent="open = false; event.target.blur()"
        x-on:keydown.arrow-down.prevent="focusNext()"
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
