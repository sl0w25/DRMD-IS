<?php

use App\Http\Controllers\LocationController;
use App\Http\Controllers\SitrepController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


<<<<<<< HEAD
Route::get('/api/municipalities/{province}', [LocationController::class, 'municipalities']);
Route::get('/api/barangays/{municipality}', [LocationController::class, 'barangays']);

Route::post('/minor_incident/sitrep', [SitrepController::class, 'store']);
=======




>>>>>>> 1e2c656c6a583bd698a746a432ef8ce29f6c791c
