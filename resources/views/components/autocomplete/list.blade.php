@props([
    'unstyled' => false,
    'inline' => false,
    'containerClass' => '',
])

<div @class([$containerClass, 'absolute z-10' => !$inline && !$unstyled, 'w-full' => !$unstyled])>
    <ul
        x-show="open"
    {{ $attributes->class(['mx-2 mt-1 border border-gray-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-900 relative overflow-auto' => !$unstyled]) }}
        x-ref="autocomplete-list"
        x-cloak>
        {{ $slot }}
    </ul>
</div>
