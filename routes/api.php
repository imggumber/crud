<?php

use App\Http\Controllers\Api\Authcontroller;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login',  [Authcontroller::class, 'login']);
Route::post('/register',  [Authcontroller::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function(){
    Route::get('/profile',  [Authcontroller::class, 'profile']);
    Route::post('/logout',  [Authcontroller::class, 'logout']);
    Route::apiResource('products', ProductController::class);
});

