<?php

namespace App\Http\Controllers\Api;

use App\Actions\Lists\AddMealToList;
use App\Http\Requests\AddMealToListRequest;
use App\Http\Resources\MealCollection;
use App\Liste;
use App\Meal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;

class ListMealController
{
    use AuthorizesRequests;

    public function index(Liste $list): MealCollection
    {
        return new MealCollection($list->meals);
    }

    public function store(Liste $list, AddMealToListRequest $request, AddMealToList $action): Response
    {
        $this->authorize('add-meal', $list);

        $action->perform(
            $list,
            Meal::findOrFail($request->input('meal_id'))
        );

        return response()->noContent();
    }
}
