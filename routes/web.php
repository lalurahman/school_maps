<?php

use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

// Route::get('/', [MapController::class, 'index'])->name('home');
Route::get('/', [MapController::class, 'sd'])->name('sd');
