@props([
    'unstyled' => false,
])

<button
    type="button"
    x-on:click="clear()"
    x-show="hasSelectedItem()"
    {{ $attributes->class(['border-2 border-gray-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-900 text-gray-700 dark:text-gray-300 transition-transform ease-in-out duration-100 transform cursor-pointer hover:scale-105 hover:text-black dark:hover:text-white focus:outline-none focus:border-blue-400' => !$unstyled]) }}
    x-cloak
    >
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @else
        <svg class="h-5 w-5 fill-current"
            viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z"></path>
        </svg>
    @endif
</button>
