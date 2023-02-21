<?php

return [
    'options' => [
        'id' => 'id',
        'text' => 'text',
        'auto-select' => true,
        'allow-new' => true,
        'load-once-on-focus' => true,
        'inline' => false,
        'inline-styles' => 'relative',
        'overlay-styles' => 'absolute z-30',
        'result-focus-styles' => 'bg-blue-500',
    ],

    'components' => [
        'outer-container' => 'outer-container',
        'input' => 'input',
        'clear-button' => 'clear-button',
        'dropdown' => 'dropdown',
        'loading' => 'loading',
        'results-container' => 'results-container',
        'prompt' => 'prompt',
        'results-list' => 'results-list',
        'add-new-row' => 'add-new-row',
        'result-row' => 'result-row',
        'no-results' => 'no-results',
    ],

    // Set this to true if you would prefer it to use the global namespace <x-autocomplete />
    'use_global_namespace' => false,
];
