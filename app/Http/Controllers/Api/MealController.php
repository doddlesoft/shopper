<?php

namespace App\Http\Controllers\Api;

use App\Actions\Meals\CreateMeal;
use App\Actions\Meals\DeleteMeal;
use App\Actions\Meals\UpdateMeal;
use App\Http\Requests\MealRequest;
use App\Http\Resources\Meal as MealResource;
use App\Http\Resources\MealCollection;
use App\Meal;
use Illuminate\Http\Response;

class MealController
{
    public function index(): MealCollection
    {
        return new MealCollection(Meal::all());
    }

    public function store(MealRequest $request, CreateMeal $action): MealResource
    {
        return new MealResource($action->perform($request->input('name')));
    }

    public function show(Meal $meal): MealResource
    {
        return new MealResource($meal);
    }

    public function update(Meal $meal, MealRequest $request, UpdateMeal $action): MealResource
    {
        return new MealResource($action->perform($meal, $request->input('name')));
    }

    public function destroy(Meal $meal, DeleteMeal $action): Response
    {
        $action->perform($meal);

        return response()->noContent();
    }
}
