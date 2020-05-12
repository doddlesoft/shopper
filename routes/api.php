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
    Route::get('items', 'ItemController@index')->name('items.index');
    Route::post('items', 'ItemController@store')->name('items.store');
    Route::patch('items/{item}', 'ItemController@update')->name('items.update');
    Route::delete('items/{item}', 'ItemController@destroy')->name('items.destroy');
    Route::post('completed-items', 'CompletedItemController@store')->name('completed-items.store');
    Route::get('lists', 'ListController@index')->name('lists.index');
    Route::post('lists', 'ListController@store')->name('lists.store');
    Route::get('lists/{list}', 'ListController@show')->name('lists.show');
    Route::patch('lists/{list}', 'ListController@update')->name('lists.update');
    Route::delete('lists/{list}', 'ListController@destroy')->name('lists.destroy');
    Route::get('meals', 'MealController@index')->name('meals.index');
    Route::post('meals', 'MealController@store')->name('meals.store');
    Route::get('meals/{meal}', 'MealController@show')->name('meals.show');
    Route::patch('meals/{meal}', 'MealController@update')->name('meals.update');
    Route::delete('meals/{meal}', 'MealController@destroy')->name('meals.destroy');
    Route::get('lists/{list}/meals', 'ListMealController@index')->name('lists.meals.index');
    Route::post('lists/{list}/meals', 'ListMealController@store')->name('lists.meals.store');
});
