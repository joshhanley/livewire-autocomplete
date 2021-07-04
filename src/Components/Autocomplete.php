<?php

namespace LivewireAutocomplete\Components;

use Illuminate\View\Component;

class Autocomplete extends Component
{
    public $name;
    public $options;
    public $components;
    public $minLength;

    public function __construct(
        $name = 'autocomplete',
        $options = [],
        $components = [],
        $minLength = 0
    ) {
        $this->name = $name;
        $this->options = array_merge(config('autocomplete.options', []), $options);
        $this->components = $components;
        $this->minLength = $minLength;
    }

    public function getOption($option)
    {
        return $this->options[$option] ?? null;
    }

    public function getComponent($componentKey)
    {
        if (isset($this->components[$componentKey])) {
            return $this->components[$componentKey];
        }

        $componentName = config('autocomplete.components.' . $componentKey, null);

        if (is_null($componentName)) {
            return;
        }

        if (config('autocomplete.use_global_namespace')) {
            return $componentName;
        }

        return 'lwc::' . $componentName;
    }

    public function shouldShowPlaceholder($results, $inputText)
    {
        return ! $this->hasResults($results) && ! $this->inputIsMinLength($inputText);
    }

    public function hasResults($results)
    {
        return is_countable($results) && count($results) > 0;
    }

    public function inputIsMinLength($inputText)
    {
        return $inputText !== null && $inputText != '' && strlen($inputText) >= $this->minLength;
    }

    public function getViewName()
    {
        if (config('autocomplete.use_global_namespace', false)) {
            return 'components.autocomplete';
        }

        return 'lwc::components.autocomplete';
    }

    public function render()
    {
        return view($this->getViewName());
    }
}
