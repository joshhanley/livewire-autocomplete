@props([
    'unstyled' => false,
])

<ul
    x-show="open"
    {{ $attributes->class([!$unstyled => 'border border-gray-300 rounded bg-white py-2']) }}>
    {{ $slot }}
</ul>
