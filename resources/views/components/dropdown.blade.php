<div {{ $attributes->class('mt-0.5 px-2 w-full') }}
    x-transition:enter="transition ease-out duration-100 origin-top"
    x-transition:enter-start="transform opacity-0 scale-y-90"
    x-transition:enter-end="transform opacity-100 scale-y-100"
    x-transition:leave="transition ease-in duration-75 origin-top"
    x-transition:leave-start="transform opacity-100 scale-y-100"
    x-transition:leave-end="transform opacity-0 scale-y-90"
    x-cloak>
    <div
        class="relative max-h-56 overflow-y-auto rounded-md border border-gray-300 bg-white shadow">
        {{ $slot }}
    </div>
</div>
