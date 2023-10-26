@props([
    'unstyled' => false,
    'inline' => false,
])

<div @class(['absolute' => !$inline, 'w-full'])>
    <ul
        x-show="open"
        {{ $attributes->class(['mx-2 mt-1 border border-gray-300 rounded bg-white relative overflow-hidden' => !$unstyled]) }}
        x-cloak>
        {{ $slot }}
    </ul>
</div>
