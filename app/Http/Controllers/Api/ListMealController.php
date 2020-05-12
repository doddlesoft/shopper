<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AddMealToListRequest;
use App\Http\Resources\MealCollection;
use App\Liste;
use App\Lists\Actions\AddMealToList;
use App\Meal;
use Illuminate\Http\Response;

class ListMealController
{
    public function index(Liste $list): MealCollection
    {
        return new MealCollection($list->meals);
    }

    public function store(Liste $list, AddMealToListRequest $request, AddMealToList $action): Response
    {
        $action->perform(
            $list,
            Meal::findOrFail($request->input('meal_id'))
        );

        return response()->noContent();
    }
}
