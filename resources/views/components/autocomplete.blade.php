<div x-data="{open: false}" x-on:click.away="open = false">
    <input x-on:focus="open = true" />
    <div x-show="open" dusk="dropdown" x-cloak>Dropdown</div>
</div>
