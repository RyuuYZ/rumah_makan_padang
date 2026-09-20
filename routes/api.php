<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\MenuItemController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Auth routes (Google Sign-In)
    Route::post('/auth/google', [AuthController::class, 'googleLogin']);

    // Public Branch routes for Mobile & Web
    Route::get('/branches', [BranchController::class, 'index']);
    Route::get('/branches/{id}', [BranchController::class, 'show']);
    Route::get('/branches-with-menu', [BranchController::class, 'branchesWithMenu']);

    // Public Menu Item routes
    Route::get('/menu-items', [MenuItemController::class, 'index']);
    Route::get('/menu-items/{id}', [MenuItemController::class, 'show']);
    Route::get('/menu-items/{id}/reviews', [ReviewController::class, 'index']);

    // Public Customer Review routes
    Route::post('/reviews', [ReviewController::class, 'store']);

    // Public Customer Order routes
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    // Protected Admin / Staff actions
    Route::post('/menu-items/upload-image', [MenuItemController::class, 'uploadImage'])->middleware('auth');
    Route::get('/orders', [OrderController::class, 'index'])->middleware('auth');
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->middleware('auth');
});
