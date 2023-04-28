<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


//Route::resource('posts', PostController::class);

// Public Routes


Route::get('posts', [PostController::class, 'index'])->name('posts');
Route::get('posts/{id}', [PostController::class, 'show']);
Route::get('posts/search/{name}', [PostController::class, 'search']);


Route::controller(AuthController::class)->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', 'logout')->middleware('auth-sanctum');
});
// Protected Routes
Route::prefix('posts')
    ->controller(PostController::class)
    ->middleware('auth-sanctum')
    ->group(function () {

        Route::withoutMiddleware('auth-sanctum')->group(function () {
            Route::get('', 'index')->name('posts');
            Route::get('{id}', 'show');
            Route::get('search/{name}', 'search');
        });

        Route::post('', 'store');
        Route::put('{id}', 'update');
        Route::delete('{id}', 'destroy');
    });
