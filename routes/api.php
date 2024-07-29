<?php

use App\Http\Controllers\ApiArticleController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::post('/test-upload', [TestController::class, 'testUpload']);
//Route::get('/test-retrieve', [TestController::class, 'testRetrieve']);
//Route::get('/test-list', [TestController::class, 'testList']);
//Route::delete('/test-delete', [TestController::class, 'testDelete']);

Route::apiResource('articles', ApiArticleController::class);
