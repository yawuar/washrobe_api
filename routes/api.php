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

Route::post('login', 'ApiController@login')->middleware('cors');
Route::post('register', 'ApiController@register')->middleware('cors');

Route::group(['middleware' => ['auth:api', 'cors']], function(){

    // return user information
    Route::post('user', 'ApiController@getUser');

    // return all the categories
    Route::post('wardrobe', 'WardrobeController@categories');
    Route::post('wardrobe/{id}', 'WardrobeController@category');
    Route::delete('wardrobe/{id}', 'WardrobeController@delete');
});
