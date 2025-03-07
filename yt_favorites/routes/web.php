<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteListController;
use App\Http\Controllers\YtController;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\HandleCors;
use App\Models\FavoriteList;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*');



Route::withoutMiddleware([VerifyCsrfToken::class])->prefix('/api')->group(function(){
        Route::post('/signup', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/videos', [YtController::class, 'getVideos'])->name('getVideos');
        Route::post('/favorites', [FavoriteListController::class, 'addToFavorites'])->name('addFavorites');
        Route::get('/listfavorites',[FavoriteListController::class,'listFavorites'])->name('listFavorites');
        Route::delete('/delete', [FavoriteListController::class, 'delete'])->name('delete');
        Route::get('/search',[YtController::class,'searchVideos'])->name('search');
        Route::get('/orderByCreatedAt',[FavoriteListController::class,'orderListByCreatedAt'])->name('orderByCreatedAt');
        Route::get('/orderByDuration',[FavoriteListController::class,'orderListByDuration'])->name('orderByDuration');
        Route::post('/watched',[ FavoriteListController::class, 'markWatched'])->name('markWatched');
        Route::get('/favorites', [FavoriteListController::class, 'checkIfExists'])->name('checkIfExists');
        Route::get('/check-token', function(){
            try{
                JWTAuth::parseToken()->authenticate();
                return response()->json(['valid'=>true],200);
            }
            catch(Exception $e){
                return response()->json(['valid'=>false],401);
            }
        });
});