@props([
    'unstyled' => false,
])

<div {{ $attributes->class(['-mr-3 -my-2 px-3 py-2' => !$unstyled]) }}>
    {{ $slot }}
</div>