<?php
;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Recommendation\RecommendationController;




Route::get('/new_recommendation', [RecommendationController::class, 'index'])->name('new_recommendation');
