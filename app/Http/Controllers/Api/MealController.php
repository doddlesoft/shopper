<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\MealRequest;
use App\Http\Resources\Meal as MealResource;
use App\Meal;
use App\Meals\Actions\CreateMeal;
use App\Meals\Actions\DeleteMeal;
use App\Meals\Actions\UpdateMeal;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class MealController
{
    public function index(): AnonymousResourceCollection
    {
        return MealResource::collection(Meal::all());
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
