<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
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

Route::prefix("auth")
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::post('logout', 'logout')->middleware('auth:sanctum');
    });

Route::prefix("users/{user}")
    ->middleware(["auth:sanctum", "checkUserId"])
    ->group(function () {
        Route::get("", [UserController::class, "getUser"]);

        Route::prefix("posts")
            ->controller(PostController::class)
            ->group(function () {
                Route::get("", "getAllPosts");
                Route::get("search", "searchPostsByTitle");
                Route::post("", "createPost");

                Route::middleware("checkResourceId")
                    ->group(function () {
                        Route::get("{post}", "getPost");
                        Route::put("{post}", "updatePost");
                        Route::delete("{post}", "deletePost");
                        Route::controller(CommentController::class)
                            ->post("{post}/comments", "createComment");
                    });

            });

        Route::prefix("comments")
            ->controller(CommentController::class)
            ->group(function () {
                Route::get("", "getAllComments");
//                Route::post("", "createComment");
            });
    });


Route::prefix("posts")
    ->controller(PostController::class)
    ->group(function () {
        Route::get("", "getAllPosts");
        Route::get("search", "searchPostsByTitle");

        Route::middleware("auth:sanctum")
            ->group(function () {
                Route::post("", "createPost");
                Route::get("{post}", "getPost");

                Route::middleware("checkResourceId")
                    ->group(function () {
                        Route::put("{post}", "updatePost");
                        Route::delete("{post}", "deletePost");

                        Route::controller(CommentController::class)
                            ->withoutMiddleware("checkResourceId")
                            ->group(function () {
                                Route::get("{post}/comments", "getAllComments");
                                Route::post("{post}/comments", "createComment");
                            });
                    });
            });


    });
