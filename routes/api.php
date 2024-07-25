<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\EmailController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\NewsController;
use App\Http\Controllers\Api\V1\PositionController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\SubCategoryController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Middleware\EnsureAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$api_v = env("API_VERSION");

Route::post($api_v . '/auth/register', [AuthController::class, 'create']);
Route::post($api_v . '/auth/login', [AuthController::class, 'login']);

Route::post($api_v . '/password/sendEmail', [PasswordResetController::class, 'sendEmail']);
Route::post($api_v . '/password/newPassword', [PasswordResetController::class, 'newPassword']);
Route::get($api_v . '/password/validateToken/{token}', [PasswordResetController::class, 'validateToken']);

Route::apiResource($api_v . '/emails', EmailController::class)->only('store');
Route::apiResource($api_v . '/categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource($api_v . '/subcategories', SubCategoryController::class)->only(['index', 'show']);

Route::middleware(['auth:sanctum'])->group(function () {
    $api_v = env("API_VERSION");

    Route::get($api_v . '/auth/getUser', [AuthController::class, 'getUser']);
    Route::get($api_v . '/auth/logout', [AuthController::class, 'logout']);

    Route::apiResource($api_v . '/roles', RoleController::class);

    Route::get($api_v . '/emails/all', [EmailController::class, 'all']);
    Route::apiResource($api_v . '/emails', EmailController::class)->except('store');

    Route::patch($api_v . '/users/change-password/{id}', [UserController::class, 'changePassword']);
    Route::apiResource($api_v . '/users', UserController::class);

    Route::apiResource($api_v . '/employees', EmployeeController::class);

    Route::apiResource($api_v . '/positions', PositionController::class);

    Route::apiResource($api_v . '/news', NewsController::class);

    Route::apiResource($api_v . '/products', ProductController::class);

    Route::apiResource($api_v . '/categories', CategoryController::class)->except('index', 'show');
    
    Route::apiResource($api_v . '/subcategories', SubCategoryController::class)->except('index', 'show');
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
