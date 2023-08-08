<?php

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

Livewire::setUpdateRoute(fn($handle) => Route::post('/endpoint', $handle));
Livewire::setScriptRoute(fn($handle) => Route::get('/control.js', $handle));

