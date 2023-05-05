<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Controller;
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

Route::prefix("user/profile")
    ->middleware("auth:sanctum")
    ->controller(UserController::class)
    ->group(function () {
        Route::post("notify-user", "sendTestNotification");

        Route::get("", "getUser")->name("user-profile");
        Route::put("edit", "updateUserCredentials");
        Route::post("generate-otp", "generateOtp")->name("get-otp");
        Route::post("verify-otp", "verifyOtp")->name("verify-otp");
    });

Route::prefix("posts")
    ->middleware("auth:sanctum")
    ->controller(PostController::class)
    ->group(function () {
        Route::get("", "getAllPosts")->withoutMiddleware("auth:sanctum")->name("all-posts");

        Route::post("", "createPost")->middleware("checkEmailVerification");
        Route::get("{post}", "getPost");
        Route::put("{post}", "updatePost")->middleware(["checkResourceId", "checkEmailVerification"]);
        Route::delete("{post}", "deletePost")->middleware(["checkResourceId", "checkEmailVerification"]);

        Route::controller(CommentController::class)
            ->group(function () {
                Route::get("{post}/comments", "getAllComments");
                Route::post("{post}/comments", "createComment");

                Route::delete("{post}/comments/{comment}", "deleteComment")
                    ->middleware("validateCommentAuthor");
            });


    });

Route::name("404")->get("/404", function () {
    return Controller::onError(message: "Unauthorised request.", status: 403);
});

