<?php

use App\Http\Controllers\Controller;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/show',[Controller::class, 'index']);
Route::get('/search/users',[Controller::class, 'searchQuery']);
Route::get('/search/user/{id}/repositories',[Controller::class, 'searchRepositoryQuery']);
Route::get('/users/{id}',[Controller::class, 'findById']);
Route::get('/users/popular',[Controller::class, 'getPopularUsers']);
