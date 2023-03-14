<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleCategoryController;
use App\Http\Controllers\AuthController;
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


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);

Route::get('/article', [ArticleController::class, 'index']);
Route::get('/article/{id}', [ArticleController::class, 'show']);
Route::middleware('auth:sanctum')->post('/article', [ArticleController::class, 'store']);
Route::middleware('auth:sanctum')->post('/article/update/{id}', [ArticleController::class, 'update']);
Route::middleware('auth:sanctum')->post('/article/delete/{id}', [ArticleController::class, 'destroy']);

Route::middleware('auth:sanctum')->get('/categories', [ArticleCategoryController::class, 'index']);
Route::middleware('auth:sanctum')->get('/categories/{id}', [ArticleCategoryController::class, 'show']);
Route::middleware('auth:sanctum')->post('/categories', [ArticleCategoryController::class, 'store']);
Route::middleware('auth:sanctum')->post('/categories/{id}', [ArticleCategoryController::class, 'update']);
Route::middleware('auth:sanctum')->post('/categories/delete/{id}', [ArticleCategoryController::class, 'destroy']);