<input
    type="text"
    autocomplete="off"
    {{ $attributes->class('w-full pl-4 py-2 rounded border border-cool-gray-200 shadow-inner leading-5 text-cool-gray-900 placeholder-cool-gray-400 focus:outline-none focus:border-blue-400 disabled:bg-cool-gray-100') }}
    x-bind:class="[selected ? 'pr-9' : 'pr-4']"
    x-bind:disabled="selected" />
