<?php

use App\Http\Controllers\LocationController;
use App\Http\Controllers\SitrepController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('create_new', function () {
    return Inertia::render('Plab/Create');
})->name('create_new');


Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/api/municipalities/{province}', [LocationController::class, 'municipalities']);
Route::get('/api/barangays/{municipality}', [LocationController::class, 'barangays']);

require __DIR__. '/sitrep_route.php';
require __DIR__. '/recommendation.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
