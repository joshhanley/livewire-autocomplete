@props([
    'unstyled' => false,
])

<div class="absolute w-full">
    <ul
        x-show="open"
        {{ $attributes->class([!$unstyled => 'border border-gray-300 rounded bg-white relative overflow-hidden']) }}>
        {{ $slot }}
    </ul>
</div>
