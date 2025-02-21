<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteListController;
use App\Http\Controllers\YtController;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\HandleCors;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::withoutMiddleware([VerifyCsrfToken::class])->group(function(){
        Route::post('/signup', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/videos', [YtController::class, 'getVideos'])->name('getVideos');
        Route::post('/favorites', [FavoriteListController::class, 'addToFavorites'])->name('addFavorites');
        Route::get('/listfavorites',[FavoriteListController::class,'listFavorites'])->name('listFavorites');
        Route::delete('/delete', [FavoriteListController::class, 'delete'])->name('delete');
        Route::get('/search',[YtController::class,'searchVideos'])->name('search');
});