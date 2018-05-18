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

Route::post('login', 'ApiController@login');
Route::post('register', 'ApiController@register');

Route::group(['middleware' => 'auth:api'], function(){

    // return user information
    Route::post('user', 'ApiController@getUser');

    // return all the categories
    Route::post('wardrobe', 'WardrobeController@categories');
});

Route::get('wardrobe/{id}', 'WardrobeController@category');
