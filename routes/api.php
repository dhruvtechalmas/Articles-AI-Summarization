<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArticleApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



// Route::apiResource('articles', ArticleApiController::class);use App\Http\Controllers\Api\ArticleApiController;

Route::post('/articles', [ArticleApiController::class, 'store'])
    ->middleware('throttle:1,1');

Route::get('/articles', [ArticleApiController::class, 'index']);
Route::get('/articles/{article}', [ArticleApiController::class, 'show']);
Route::delete('/articles/{article}', [ArticleApiController::class, 'destroy']);