<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

if (env('APP_ENV') == 'prod') {
    URL::forceScheme('https');
}
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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Route::group(['namespace' => 'Api'], function () {
//    Route::get('v1/ads', 'AdController@getOne')->name('getOne');
//});

Route::get('v1/ads/{id}', 'AdController@getOne')->name('getOne');
Route::get('v1/ads', 'AdController@getAll')->name('getAll');
Route::post('v1/ads', 'AdController@create')->name('create');
