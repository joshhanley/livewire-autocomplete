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
        'outer_container' => 'outer-container',
        'input' => 'input',
        'clear_button' => 'clear-button',
        'dropdown' => 'dropdown',
        'loading' => 'loading',
        'results_container' => 'results-container',
        'prompt' => 'prompt',
        'results_list' => 'results-list',
        'add_new_row' => 'add-new-row',
        'result_row' => 'result-row',
        'no_results' => 'no-results',
    ],

    // Set this to true if you would prefer it to use the global namespace <x-autocomplete />
    'use_global_namespace' => false,
];
