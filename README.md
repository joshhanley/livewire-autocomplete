# Livewire Autocomplete

An autocomplete select component designed for use with Livewire that allows you to search through results or add a new one.

## Requirements

- Laravel ^7.0.0
- Livewire ^2.3.6
- Alpine ^2.8.1

## Installation

To install the package run

```bash
composer require joshhanley/livewire-autocomplete
```

Then include the scripts by putting this tag inside your app layout after `<livewire:scripts />` or you can push it to your scripts stack.

```html
<x-autocomplete-scripts />
```

## Usage

This autocomplete component is a blade component design to be used within a Livewire component. It won't work as a standalone component, without Livewire.

## Example API

```html
<x-autocomplete
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
        'outer_container' => 'my-outer-container',
        'input' => 'my-input',
        'clear_button' => 'my-clear-button',
        'dropdown' => 'my-dropdown',
        'loading' => 'my-loading',
        'results_container' => 'my-results-container',
        'prompt' => 'my-prompt',
        'results_list' => 'my-results-list',
        'add_new_row' => 'my-add-new-row',
        'result_row' => 'my-client-result-row',
        'no_results' => 'my-no-results',
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
php artisan autocomplete:publish --components
```

- **outer_container** surrounds the whole autocomplete input and dropdown

- **input** is the input element

- **clear_button** is the clear button for clearing an existing selection

- **dropdown** is the dropdown box

- **loading** is the loading contents, shown when livewire is loading

- **results_container** surrounds the prompt, results, and no results components

- **prompt** is the prompt shown in the empty box, when it's open (when `wire:focus` not set and `results = null`)

- **results_list** surrounds only the add new row and the result rows

- **add_new_row** is the "add new" result prompt row (when `allow-new = true`)

- **result_row** is the component to use for each of the result rows

- **no_results** is when there are no results found (when `allow-new = false`)


**Structure**

Below is the structure of how all the components are laid out, so you know which components to customise (if desired)

```html
<outer_container>
    <input />

    <clear_button />

    <dropdown>
        <loading />

        <results_container>
            <prompt />

            <results_list>
                <add_new_row />

                <result_row />
            </results_list>

            <no_results />
        </results_container>
    </dropdown>
</outer_container>
```

**Default Components**

https://github.com/joshhanley/livewire-autocomplete/tree/main/resources/views/components

## Config

Config can be published using
```bash
php artisan autocomplete:publish --config
```

**Default Config**

https://github.com/joshhanley/livewire-autocomplete/blob/main/config/autocomplete.php

## Styles

The default styles on this component use Tailwind, but they can be overridden by:
- publishing the components and changing them;
- publishing the config and setting custom component names to use in the config; or
- manually passing in component names to each instance of the autocomplete component through the `components` prop
