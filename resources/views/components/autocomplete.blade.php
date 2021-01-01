@props([
    'resultsProperty',
])
<div x-data="{open: false}" x-on:click.away="open = false">
    <input x-on:focus="open = true" x-on:keydown.escape.prevent="open = false; event.target.blur()" dusk="autocomplete-input" />

    <div x-show="open" dusk="autocomplete-dropdown" x-cloak>
        @foreach($this->$resultsProperty as $key => $result)
            <div>{{ $result }}</div>
        @endforeach
    </div>
</div>
