<?php

use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\MenuItemController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Branch routes
    Route::get('/branches', [BranchController::class, 'index']);
    Route::get('/branches/{id}', [BranchController::class, 'show']);
    Route::get('/branches-with-menu', [BranchController::class, 'branchesWithMenu']);

    // Menu Item routes
    Route::get('/menu-items', [MenuItemController::class, 'index']);
    Route::post('/menu-items/upload-image', [MenuItemController::class, 'uploadImage']);
    Route::get('/menu-items/{id}', [MenuItemController::class, 'show']);

    // Order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
});
