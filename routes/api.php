<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Login / Auth routes
require __DIR__.'/auth.php';

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->post('/applications', [ApplicationController::class, 'store']);
Route::middleware(['auth:sanctum'])->put('/applications/{id}', [ApplicationController::class, 'update']);