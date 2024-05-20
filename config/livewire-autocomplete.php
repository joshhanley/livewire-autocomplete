<?php

return [
    /*
     * Set this to `true` if you want to use the global namespace
     * `<x-autocomplete />` or `false` if you want to use the
     * package namespace `<x-lwa-autocomplete />`
     *
     * This is set to true by default
     */
    'use_global_namespace' => true,

    'inline-scripts' => true,

    'legacy_options' => [
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

    'legacy_components' => [
        'result-row' => 'result-row',
    ],
];
