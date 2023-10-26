@props([
    'unstyled' => false,
    'inline' => false,
])

<div @class(['absolute' => !$inline, 'w-full'])>
    <ul
        x-show="open"
        {{ $attributes->class([!$unstyled => 'border border-gray-300 rounded bg-white relative overflow-hidden']) }}>
        {{ $slot }}
    </ul>
</div>
