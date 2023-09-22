<?php

use Illuminate\Support\Facades\Route;
use LivewireAutocomplete\Controllers\AssetController;

Route::get('/livewire-autocomplete/{file}', AssetController::class)
    ->where('file', '.*')
    ->name('livewire-autocomplete.asset');
