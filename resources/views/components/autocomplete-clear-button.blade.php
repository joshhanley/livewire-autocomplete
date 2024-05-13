<div {{ $attributes->class(['absolute right-0 inset-y-0 pr-3 flex items-center']) }} x-cloak>
    <button
        type="button"
        x-on:click="clear()"
        x-show="hasSelectedItem()"
        class="border-2 border-gray-300 rounded bg-white text-gray-700 transition-transform ease-in-out duration-100 transform hover:scale-105 hover:text-black focus:outline-none focus:border-blue-400"
        >
        {{ $slot }}
    </button>
</div>
