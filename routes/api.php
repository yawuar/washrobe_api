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

Route::group(['middleware' => ['auth:api']], function(){

    // return user information
    Route::post('user', 'ApiController@getUser');

    // return all the categories
    Route::prefix('wardrobe')->group(function () {
        Route::post('', 'WardrobeController@categories');
        Route::post('{id}', 'WardrobeController@category');
        Route::delete('{id}', 'WardrobeController@delete');
    });

    Route::prefix('item')->group(function () {
        Route::post('{item_id}', 'ItemController@addItemToUser');
        Route::post('hash/{item_id}', 'ItemController@encodeItem');
        Route::post('get/{item_id}', 'ItemController@getItemById');
    });

    Route::prefix('laundry')->group(function () {
        Route::post('', 'LaundryController@categories');
        Route::post('get', 'LaundryController@getLaundryByUser');
        Route::post('{id}', 'LaundryController@putInLaundry');
        Route::delete('{id}', 'LaundryController@deleteLaundryById');
        Route::post('item/{id}', 'LaundryController@getLaundryById');

        Route::post('items/sort', 'LaundryController@sort');
    });
});
