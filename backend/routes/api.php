<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



use App\Http\Controllers\AuthController;

// Ensure that the 'api' middleware is set for API routes
Route::middleware('api')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

use App\Http\Controllers\ProductController;

Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('products', [ProductController::class, 'store']); // Create
    Route::put('products/{id}', [ProductController::class, 'update']); // Update
    Route::delete('products/{id}', [ProductController::class, 'destroy']); // Delete
});



use App\Http\Controllers\CartController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('cart', [CartController::class, 'addToCart']);
    Route::get('cart', [CartController::class, 'viewCart']);
});


use App\Http\Controllers\OrderController;

Route::middleware('auth:sanctum')->post('order', [OrderController::class, 'placeOrder']);
