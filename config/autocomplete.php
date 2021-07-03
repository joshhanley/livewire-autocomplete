<?php

return [

    'options' => [
        'id' => 'id',
        'text' => 'text',
        'auto_select' => true,
        'allow_new' => true,
        'inline' => false,
        'inline_styles' => 'relative',
        'overlay_styles' => 'absolute z-30',
        'result_focus_styles' => 'bg-blue-500',
    ],

    'components' => [
        'input' => 'autocomplete.input',
        'dropdown' => 'autocomplete.dropdown',
        'prompt' => 'autocomplete.prompt',
        'loading' => 'autocomplete.loading',
        'no_results' => 'autocomplete.no-results',
        'add_new_row' => 'autocomplete.add-new-row',
        'result_row' => 'autocomplete.result-row',
    ],

    // Set this to true if you would prefer it to use the global namespace <x-autocomplete />
    'use_global_namespace' => false,

];
