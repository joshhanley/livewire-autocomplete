@props([
    'unstyled' => false,
])

<label {{ $attributes->class(['' => !$unstyled]) }}>
    {{ $slot }}
</label>
