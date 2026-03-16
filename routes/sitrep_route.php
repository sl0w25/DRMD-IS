<?php

use App\Http\Controllers\SitrepController;
use Illuminate\Support\Facades\Route;

Route::get('/minor_incident', [SitrepController::class, 'index'])->name('minor_incident');

Route::get('/minor_incident/{id}/print', [SitrepController::class, 'print'])
    ->name('minor_incident.print');
