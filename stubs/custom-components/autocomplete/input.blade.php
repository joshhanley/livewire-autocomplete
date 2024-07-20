<x-lwa::autocomplete.input :attributes="$attributes->class('')" unstyled>
    @if (isset($prefix))
        <x-slot:prefix>
            {{ $prefix }}
        </x-slot>
    @endif

    {{ $slot }}

    @if (isset($suffix))
        <x-slot:suffix>
            {{ $suffix }}
        </x-slot>
    @endif
</x-lwa::autocomplete.input>
