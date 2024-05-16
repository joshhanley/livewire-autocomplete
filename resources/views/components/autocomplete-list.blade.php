@props([
    'unstyled' => false,
    'inline' => false,
    'containerClass' => '',
])

<div @class([$containerClass, 'absolute z-10' => !$inline && !$unstyled, 'w-full' => !$unstyled])>
    <ul
        x-show="open"
        {{ $attributes->class(['mx-2 mt-1 border border-gray-300 rounded bg-white relative overflow-auto' => !$unstyled]) }}
        x-cloak>
        {{ $slot }}
    </ul>
</div>
