<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api'], function () {
    Route::get('/test', 'IndexController@test');
    Route::post('/Login/login', 'LoginController@login')->name('admin.login');
});

Route::group(['namespace' => 'Api', 'middleware' => 'auth:admin'], function () {
    Route::post('/Login/logout', 'LoginController@logout')->name('login.logout');
    Route::post('/Login/verifyMfa', 'LoginController@verifyMfa')->name('login.verifyMfa');
    Route::get('/Index/sidebar', 'IndexController@sidebar')->name('index.sidebar');
});

