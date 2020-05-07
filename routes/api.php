<?php

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

Route::namespace('Api')->group(function () {
    Route::get('lists', 'ListController@index')->name('lists.index');
    Route::post('lists', 'ListController@store')->name('lists.store');
    Route::get('lists/{list}', 'ListController@show')->name('lists.show');
    Route::patch('lists/{list}', 'ListController@update')->name('lists.update');
    Route::delete('lists/{list}', 'ListController@destroy')->name('lists.destroy');
    Route::get('lists/{list}/items', 'ListItemController@index')->name('lists.items.index');
    Route::post('lists/{list}/items', 'ListItemController@store')->name('lists.items.store');
    Route::patch('lists/{list}/items/{item}', 'ListItemController@update')->name('lists.items.update');
    Route::delete('lists/{list}/items/{item}', 'ListItemController@destroy')->name('lists.items.destroy');
});
