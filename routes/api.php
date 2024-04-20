<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [LoginController::class, 'authenticate'])->name('api.login');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('api.register');

// prefix posts
Route::prefix('posts')->name('api.posts.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/{slug}', [PostController::class, 'show'])->name('show');
    Route::post('/filter', [PostController::class, 'filter'])->name('filter');
});

// prefix user has prefix posts like store, update and delete
Route::prefix('user')->name('api.user.')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('posts')->name('posts.')->group(function () {
        Route::get('/', [PostController::class, 'userPosts'])->name('index');
        Route::post('/', [PostController::class, 'store'])->name('store');
        Route::put('/{post}', [PostController::class, 'update'])->name('update');
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('delete');
        Route::post('/{post}/like', [LikeController::class, 'action'])->name('like.actions');
    });

    Route::prefix('comments')->name('comments.')->group(function () {
        Route::post('/', [CommentController::class, 'store'])->name('store');
        Route::put('/{comment}', [CommentController::class, 'update'])->name('update');
        Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('delete');
    });
});

