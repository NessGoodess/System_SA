<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\UserProfileController;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('logout', [AuthController::class, 'destroy']);
    Route::get('logoutAllDevices', [AuthController::class, 'logoutAllDevices']);

    Route::get('/profile', [UserProfileController::class, 'edit']);
    Route::put('/profile', [UserProfileController::class, 'update']);
    Route::delete('/profile', [UserProfileController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::get('/documents/create', [DocumentController::class, 'create']);
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::get('/documents/{document}', [DocumentController::class, 'show']);
    Route::get('/documents/{document}/edit', [DocumentController::class, 'edit']);
    Route::patch('/documents/{document}', [DocumentController::class, 'update']);
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);
    Route::match(['get', 'post'], '/filters', [DocumentController::class, 'filters']);
    Route::get('/search', [DocumentController::class, 'search']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [AdminProfileController::class, 'index']);
    Route::post('/users/register', [AdminProfileController::class, 'store']);
    Route::get('/users/{user}', [AdminProfileController::class, 'show']);
    Route::delete('/users/{user}', [AdminProfileController::class, 'destroy']);
    Route::get('/dashboard', [DocumentController::class, 'controlPanel']);
    Route::get('/activities', [ActivityController::class, 'index']);
});
