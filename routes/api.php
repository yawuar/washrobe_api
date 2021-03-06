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
Route::post('getUser', 'ApiController@getUserByEmail');
Route::post('register', 'ApiController@register');

Route::group(['middleware' => ['auth:api']], function(){

    // return user information
    Route::post('user', 'ApiController@getUser');

    Route::post('logout','ApiController@logout');

    Route::post('coinwash', 'CoinWashController@getCoinWash');

    // return all the categories
    Route::prefix('wardrobe')->group(function () {
        Route::post('', 'WardrobeController@categories');
        Route::post('{id}', 'WardrobeController@category');
        Route::delete('{id}', 'WardrobeController@delete');
        Route::post('get/{id}', 'WardrobeController@getItemById');
    });

    Route::prefix('item')->group(function () {
        Route::post('{item_id}', 'ItemController@addItemToUser');
        Route::post('hash/{item_id}', 'ItemController@encodeItem');
        Route::post('get/{item_id}', 'ItemController@getItemById');
        Route::post('getHash/{hash}', 'ItemController@getItemByHash');
    });

    Route::prefix('laundry')->group(function () {
        Route::post('', 'LaundryController@categories');
        Route::post('get', 'LaundryController@getLaundryByUser');
        Route::post('{id}', 'LaundryController@putInLaundry');
        Route::put('{id}', 'LaundryController@updateWashCoinId');
        Route::put('update/isWashed/{id}', 'LaundryController@updateIsWashed');
        Route::delete('{id}', 'LaundryController@deleteLaundryById');
        Route::post('item/{id}', 'LaundryController@getLaundryById');

        Route::post('items/sort', 'LaundryController@sort');
    });

    Route::prefix('calendar')->group(function () {
        Route::post('', 'CalendarController@addClothToCalendar');
        Route::post('{day}', 'CalendarController@getClothesOfUserByDay');
        Route::post('item/remove', 'CalendarController@removeItemFromCalendar');
    });
});
