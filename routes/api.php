<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentFileController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::get('/user', function (Request $request) {
    $user = $request->user();

    return [
            'name' => $user->name,
            'role' => $user->getRoleNames()->first(),
            'permission' => $user->getPermissionNames(),
        ];
})->middleware('auth:sanctum');

/**
 * API Routes for user authentication.
 */
Route::post('login', [AuthController::class, 'store']);
Route::get('/check_token', [Authcontroller::class, 'checkToken']);

/**
 * API Routes for user profile and authentication management.
 *
 * These routes allow users to interact with their profile and manage their authentication sessions:
 * - Logout from the current session or all devices.
 * - View and update their profile information.
 * - Delete their profile.
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserProfileController::class, 'edit']);
    Route::put('/profile', [UserProfileController::class, 'update']);
    Route::delete('/profile', [UserProfileController::class, 'destroy']);
    Route::post('logout', [AuthController::class, 'destroy']);
    Route::post('logoutAllDevices', [AuthController::class, 'logoutAllDevices']);
});

/**
 * API Routes for document management.
 *
 * These routes allow users to create, read, update, and delete documents.
 * They also include routes for filtering and searching documents, as well as managing comments.
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::get('/documents/formMetaData', [DocumentController::class, 'formMetaData']);

    Route::middleware('auth:sanctum', 'permission:create')->group(function () {
        Route::get('/documents/create', [DocumentController::class, 'create']);
        Route::post('/documents', [DocumentController::class, 'store']);
    });

    Route::middleware('auth:sanctum', 'permission:read')->group(function () {
        Route::get('/documents/{document}', [DocumentController::class, 'show']);
    });

    Route::middleware('auth:sanctum', 'permission:update')->group(function () {
        Route::get('/documents/{document}/edit', [DocumentController::class, 'edit']);
        Route::patch('/documents/{document}', [DocumentController::class, 'update']);
    });

    Route::middleware('auth:sanctum', 'permission:delete')->group(function () {
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);
    });



    Route::middleware('auth:sanctum', 'permission:read')->group(function () {
        Route::match(['get', 'post'], '/filters', [DocumentController::class, 'filters']);
        Route::get('/search', [DocumentController::class, 'search']);
    });

    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('auth:sanctum', 'permission:update')->group(function () {
        Route::post('/documents/{document}/files', [DocumentFileController::class, 'store']);
    });
    Route::middleware('auth:sanctum', 'permission:delete')->group(function () {
        Route::delete('/documents/{document}/files/{file}', [DocumentFileController::class, 'destroy']);
    });
    Route::middleware(['permission:read'])->group(function () {
        Route::get('/documents/{document}/files/{file}/preview', [DocumentFileController::class, 'show']);
        Route::get('/documents/{document}/files/{file}/download', [DocumentFileController::class, 'download']);
    });

});


/**
 * API Routes for admin profile management.
 *
 * These routes allow administrators to manage user profiles and access the control panel.
 * They include routes for viewing, creating, updating, and deleting user profiles.
 */
Route::middleware('auth:sanctum', 'role:admin')->group(function () {
    Route::get('/users', [AdminProfileController::class, 'index']);
    Route::post('/users/register', [AdminProfileController::class, 'store']);
    Route::get('/users/{user}', [AdminProfileController::class, 'show']);
    Route::put('/users/{user}', [AdminProfileController::class, 'update']);
    Route::delete('/users/{user}', [AdminProfileController::class, 'destroy']);
    Route::get('/dashboard', [DocumentController::class, 'controlPanel']);
    Route::get('/activities', [ActivityController::class, 'index']);
    Route::get('/notifications', [AdminProfileController::class, 'sendNotification']);
});
