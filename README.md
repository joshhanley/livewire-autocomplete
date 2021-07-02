# Livewire Autocomplete

An autocomplete select component designed for use with Livewire that allows you to search through results or add a new one.

## Requirements

- Laravel ^7.0.0
- Livewire ^2.3.6
- Alpine ^2.8.1

## Installation

To install the package run

```bash
composer require joshhanley/autocomplete
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
    wire:model-id="clientId"
    wire:model-text="clientName"
    wire:model-results="clients"
    wire:focus="getClients"
    :options="[
        'id' => 'id',
        'text' => 'name',
        'allow_new' => true,
        'inline' => true,
        'inline_styles' => 'relative',
        'overlay_styles' => 'absolute z-30',
    ]"
    :components="[
        'input' => 'my-input',
        'dropdown' => 'my-dropdown',
        'prompt' => 'my-prompt',
        'loading' => 'my-loading',
        'no_results' => 'my-no-results',
        'add_new_row' => 'my-add-new-row',
        'result_row' => 'my-client-result-row',
    ]"
/>
```

## Wire Bindings

**wire:model-id** this is the property on the Livewire component that should be populated with the selected ID

**wire:model-text** this is the property on the Livewire component that should be populated with the text value of the input field

**wire:model-result** this is the property on the Livewire component that contains the list of results.
This can be an array or collection of values, array with keys, or eloquent models

**wire:focus** this is the method on the Livewire component that should be called on focus to load results into the results property

## Options and Components

Options and components have defaults set in the config file, which can be published and overridden changing all autocomplete components.
Then individual options and components can be passed into each instance of the component through the props, and an array merge happens inside of the autocomplete class to populate any overrides.

### Options

**id** this is set to `id` by default but can be mapped to a different property on an array or model (e.g. `slug`)

**text** this is set to `text` by default but can be mapped to a different property on an array or model (e.g. `name`)

**allow_new** `true` by default.
- If `allow_new` is true, the first option will be `Add new client "Bob"`, which tab autoselects
- If `allow_new` is false, the first option is a result, which tab autoselects

**inline** this is a quick styling toggle between displaying the dropdown box inline or as an overlay.

**inline_styles** the styles to use when displaying the dropdown inline.

**overlay_styles** the styles to use when displaying the dropdown as an overlay.


### Components

**input** what component to use for the input element

**dropdown** what component to use for the dropdown box

**prompt** what component to use for the prompt shown in the empty box, when it's open (when `wire:focus` not set and `results = null`)

**loading** what component to use for loading, when it's open

**no_results** what component to use when there are no results found (when `allow_new = false`)

**add_new_row** what component to use for the "add new" result row (when `allow_new = true`)

**result_row** what component to use for each of the result rows

## Config

Config can be published using
```bash
php artisan autocomplete:publish --config
```

Default config

https://github.com/joshhanley/livewire-autocomplete/blob/main/config/autocomplete.php

## Views

Views can be published to your `resources/views/vendor/package/autocomplete` using
```bash
php artisan autocomplete:publish --views
```

## Styles

The default styles on this component use Tailwind, but they can be overridden by:
- publishing the views and changing them;
- publishing the config and setting custom component names to use in the config; or
- manually passing in component names to each instance of the autocomplete component through the `components` prop
