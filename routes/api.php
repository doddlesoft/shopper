<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Api')->group(function () {
    Route::post('register', 'RegisterController')->name('register');
    Route::post('sign-in', 'SignInController')->name('sign-in');

    Route::group(['middleware' => ['auth:sanctum']], function () {
        // Items
        Route::get('items', 'ItemController@index')->middleware('can:viewAny,App\Item')->name('items.index');
        Route::post('items', 'ItemController@store')->middleware('can:create,App\Item')->name('items.store');
        Route::patch('items/{item}', 'ItemController@update')->middleware('can:update,item')->name('items.update');
        Route::delete('items/{item}', 'ItemController@destroy')->middleware('can:delete,item')->name('items.destroy');
        Route::post('completed-items', 'CompletedItemController@store')->name('completed-items.store');
        Route::delete('completed-items/{item}', 'CompletedItemController@destroy')->name('completed-items.destroy');

        // Lists
        Route::get('lists', 'ListController@index')->name('lists.index');
        Route::post('lists', 'ListController@store')->middleware('can:create,App\Liste')->name('lists.store');
        Route::get('lists/{list}', 'ListController@show')->middleware('can:view,list')->name('lists.show');
        Route::patch('lists/{list}', 'ListController@update')->middleware('can:update,list')->name('lists.update');
        Route::delete('lists/{list}', 'ListController@destroy')->middleware('can:delete,list')->name('lists.destroy');
        Route::get('list-meals/{list}', 'ListMealController@index')->middleware('can:view,list')->name('list-meals.index');
        Route::post('list-meals/{list}', 'ListMealController@store')->name('list-meals.store');
        Route::delete('list-meals/{list}', 'ListMealController@destroy')->name('list-meals.destroy');

        // Meals
        Route::get('meals', 'MealController@index')->name('meals.index');
        Route::post('meals', 'MealController@store')->name('meals.store');
        Route::get('meals/{meal}', 'MealController@show')->middleware('can:view,meal')->name('meals.show');
        Route::patch('meals/{meal}', 'MealController@update')->middleware('can:update,meal')->name('meals.update');
        Route::delete('meals/{meal}', 'MealController@destroy')->middleware('can:delete,meal')->name('meals.destroy');
    });
});
