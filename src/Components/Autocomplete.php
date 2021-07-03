<?php

namespace LivewireAutocomplete\Components;

use Illuminate\View\Component;

class Autocomplete extends Component
{
    public $inputProperty;
    public $resultsProperty;
    public $selectedProperty;

    public $name;
    public $resultComponent;
    public $resultsPlaceholder;
    public $noResults;
    public $searchAttribute;
    public $autoselect;
    public $inline;
    public $minLength;

    public function __construct($name = null, $resultComponent = null, $resultsPlaceholder = 'Start typing to search...', $noResults = 'There were no results found', $searchAttribute = null, $autoselect = null, $inline = null, $minLength = 0)
    {
        $this->name = $name;
        $this->resultComponent = $resultComponent;
        $this->resultsPlaceholder = $resultsPlaceholder;
        $this->noResults = $noResults;
        $this->searchAttribute = $searchAttribute;
        $this->autoselect = $autoselect;
        $this->inline = $inline;
        $this->minLength = $minLength;
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
