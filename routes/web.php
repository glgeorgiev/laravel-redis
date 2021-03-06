<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('', function () {
    return view('presentation');
});

Route::get('index', 'PageController@index');
Route::get('cache-articles', 'PageController@indexWithArticlesCache');
Route::get('cache-view', 'PageController@indexWithViewCache');
Route::get('redis-code', 'PageController@indexWithRawRedisCode');
