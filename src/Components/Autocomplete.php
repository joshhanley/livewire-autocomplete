<?php

namespace LivewireAutocomplete\Components;

use Illuminate\View\Component;

class Autocomplete extends Component
{
    public $inputProperty;
    public $resultsProperty;
    public $selectedProperty;

    public $options;

    public $name;
    public $resultComponent;
    public $resultsPlaceholder;
    public $noResults;
    public $searchAttribute;
    public $inline;
    public $minLength;

    public function __construct(
        $options = [],
        $name = null,
        $resultComponent = null,
        $resultsPlaceholder = 'Start typing to search...',
        $noResults = 'There were no results found',
        $searchAttribute = null,
        $inline = null,
        $minLength = 0
    ) {
        $this->options = array_merge(config('autocomplete.options', []), $options);
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
