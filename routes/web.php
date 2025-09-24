<?php

use App\Http\Controllers\ExportController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MapController::class, 'index'])->name('home');
// Route::get('/', [MapController::class, 'sd'])->name('sd');

// Export routes
Route::get('/export/{bentuk_pendidikan}/{kecamatan}', [ExportController::class, 'export']);
Route::get('/export/by-location', [ExportController::class, 'exportByLocation'])->name('export.by-location');
Route::get('/export/sd-tamalate/by-location', [ExportController::class, 'exportSDTamalateByLocation'])->name('export.sd-tamalate.by-location');
