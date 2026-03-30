<?php

use App\Http\Controllers\LocationController;
use App\Http\Controllers\SitrepController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/api/municipalities/{province}', [LocationController::class, 'municipalities']);
Route::get('/api/barangays/{municipality}', [LocationController::class, 'barangays']);

Route::post('/minor_incident/sitrep', [SitrepController::class, 'store'])->middleware('auth');

Route::post('/sitreps/{sitrep}/submit', [SitrepController::class, 'submitAndEmail'])->middleware('auth');

Route::post('/sitreps/{sitrep}/upload-approved', [SitrepController::class, 'UploadApprovedSitrep'])->middleware('auth');



require __DIR__. '/sitrep_route.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
