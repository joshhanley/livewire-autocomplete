<?php

namespace LivewireAutocomplete\Components;

use Illuminate\View\Component;

class Autocomplete extends Component
{
    public $inputProperty;
    public $resultsProperty;
    public $selectedProperty;

    public $options;
    public $components;

    public $name;
    public $resultComponent;
    public $resultsPlaceholder;
    public $noResults;
    public $searchAttribute;
    public $inline;
    public $minLength;

    public function __construct(
        $options = [],
        $components = [],
        $name = null,
        $resultComponent = null,
        $resultsPlaceholder = 'Start typing to search...',
        $noResults = 'There were no results found',
        $searchAttribute = null,
        $inline = null,
        $minLength = 0
    ) {
        $this->options = array_merge(config('autocomplete.options', []), $options);
        $this->components = $components;
        $this->name = $name;
        $this->resultComponent = $resultComponent;
        $this->resultsPlaceholder = $resultsPlaceholder;
        $this->noResults = $noResults;
        $this->searchAttribute = $searchAttribute;
        $this->inline = $inline;
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
