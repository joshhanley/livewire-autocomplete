# Livewire Autocomplete

An autocomplete select component designed for use with Livewire that allows you to search through results or add a new one.

## Requirements

- Laravel ^8.0.0
- Livewire ^2.3.6
- Alpine ^2.8.1 | ^3.0.6 (must have Livewire ^2.5.0)

## Installation

To install the package run

```bash
composer require joshhanley/livewire-autocomplete
```

<!-- Then include the scripts by putting this tag inside your app layout after `<livewire:scripts />` or you can push it to your scripts stack.

```html
<x-autocomplete-scripts />
``` -->

## Usage

This autocomplete component is a blade component design to be used within a Livewire component. It won't work as a standalone component, without Livewire.

## Demo App

The source code for a demo of this Livewire Autocomplete component can be found here here https://github.com/joshhanley/livewire-autocomplete-demo

## Example API

```html
<x-lwa::autocomplete
    name="my-autocomplete"
    wire:model-id="clientId"
    wire:model-text="clientName"
    wire:model-results="clients"
    wire:focus="getClients"
    :options="[
        'id' => 'id',
        'text' => 'name',
        'auto-select' => true,
        'allow-new' => true,
        'load-once-on-focus' => true,
        'inline' => true,
        'inline-styles' => 'relative',
        'overlay-styles' => 'absolute z-30',
        'result-focus-styles' => 'bg-blue-500',
    ]"
    :components="[
        'outer-container' => 'my-outer-container',
        'input' => 'my-input',
        'clear-button' => 'my-clear-button',
        'dropdown' => 'my-dropdown',
        'loading' => 'my-loading',
        'results-container' => 'my-results-container',
        'prompt' => 'my-prompt',
        'results-list' => 'my-results-list',
        'add-new-row' => 'my-add-new-row',
        'result-row' => 'my-client-result-row',
        'no-results' => 'my-no-results',
    ]"
/>
```

## Wire Bindings

- `wire:model-id` this is the property on the Livewire component that should be populated with the selected ID

- `wire:model-text` this is the property on the Livewire component that should be populated with the text value of the input field

- `wire:model-result` this is the property on the Livewire component that contains the list of results.
This can be an array or collection of values, array with keys, or eloquent models

- `wire:focus` this is the method on the Livewire component that should be called on focus to load results into the results property

## Options and Components

Options and components have defaults set in the config file, which can be published and overridden changing all autocomplete components.
Then individual options and components can be passed into each instance of the component through the props, and an array merge happens inside of the autocomplete class to populate any overrides.

### Options

- **id** this is set to `id` by default but can be mapped to a different property on an array or model (e.g. `slug`)

- **text** this is set to `text` by default but can be mapped to a different property on an array or model (e.g. `name`)

- **auto-select** `true` by default.
    - If is true, there will always be a highlighted result and enter or tab will auto select that result.

- **allow-new** `true` by default.
    - If `allow-new` is true, the first option will be `Add new client "Bob"`, which tab autoselects
    - If `allow-new` is false, the first option is a result, which tab autoselects

- **load-once-on-focus** `true` by default.
    - If is true, and there is a `wire:focus` binding, then the action will only be called on the first focus of the input box. This essentially allows deferring of loading results until it is needed.

- **inline** this is a quick styling toggle between displaying the dropdown box inline or as an overlay.

- **inline-styles** the styles to use when displaying the dropdown inline.

- **overlay-styles** the styles to use when displaying the dropdown as an overlay.

- **result-focus-styles** the styles to use on a result row when it has focus.


### Components

Components can be published to your `resources/views/vendor/package/autocomplete/components` using
```bash
php artisan vendor:publish --provider="LivewireAutocomplete\LivewireAutocompleteServiceProvider" --tag="autocomplete-components"
```

- **outer-container** surrounds the whole autocomplete input and dropdown

- **input** is the input element

- **clear-button** is the clear button for clearing an existing selection

- **dropdown** is the dropdown box

- **loading** is the loading contents, shown when livewire is loading

- **results-container** surrounds the prompt, results, and no results components

- **prompt** is the prompt shown in the empty box, when it's open (when `wire:focus` not set and `results = null`)

- **results-list** surrounds only the add new row and the result rows

- **add-new-row** is the "add new" result prompt row (when `allow-new = true`)

- **result-row** is the component to use for each of the result rows

- **no-results** is when there are no results found (when `allow-new = false`)


**Structure**

Below is the structure of how all the components are laid out, so you know which components to customise (if desired)

```html
<outer-container>
    <input />

    <clear-button />

    <dropdown>
        <loading />

        <results-container>
            <prompt />

            <results-list>
                <add-new-row />

                <result-row />
            </results-list>

            <no-results />
        </results-container>
    </dropdown>
</outer-container>
```

**Default Components**

https://github.com/joshhanley/livewire-autocomplete/tree/main/resources/views/components

## Config

Config can be published using
```bash
php artisan vendor:publish --provider="LivewireAutocomplete\LivewireAutocompleteServiceProvider" --tag="autocomplete-config"
```

If you wish to use the global namespace `<x-autocomplete>` instead of `<x-lwa::autocomplete>` then you can set `'use_global_namespace' => true,` in your config.

**Default Config**

https://github.com/joshhanley/livewire-autocomplete/blob/main/config/autocomplete.php

## Styles

The default styles on this component use Tailwind, but they can be overridden by:
- publishing the components and changing them;
- publishing the config and setting custom component names to use in the config; or
- manually passing in component names to each instance of the autocomplete component through the `components` prop

## Scripts

Livewire Autocomplete scripts are automatically included whenever you have an `<x-autocomplete>` component on the page.

But the scripts don't load, if the autocomplete component isn't displayed on page render.

To get around this, you can disable the inline scripts by setting the config `autocomplete.inline-scripts` to `false`.

You can then either include the script in your `app.blade.php` layout file at the end of the body tag, after Livewire's scripts.

```blade
    <livewire:scripts />
    <script src="{{ route('livewire-autocomplete.asset', 'autocomplete.js') }}"></script>
</body>
```

Or you can include the autocomplete scripts in your `app.js` bundle.

```js
window.autocomplete = require('../../vendor/joshhanley/livewire-autocomplete/resources/js/autocomplete.js')
```
