<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Resources\Item as ItemResource;
use App\Http\Resources\ItemCollection;
use App\Item;
use App\Items\Actions\CreateItem;
use App\Items\Actions\DeleteItem;
use App\Items\Actions\UpdateItem;
use App\Liste;
use App\Meal;
use Illuminate\Http\Response;

class ItemController
{
    public function index(): ItemCollection
    {
        if (request()->filled('list_id')) {
            return new ItemCollection(Liste::findOrFail(request()->query('list_id'))->items);
        }

        if (request()->filled('meal_id')) {
            return new ItemCollection(Meal::findOrFail(request()->query('meal_id'))->items);
        }

        return new ItemCollection(Item::all());
    }

    public function store(CreateItemRequest $request, CreateItem $action): ItemResource
    {
        if ($request->filled('list_id')) {
            $action->for(Liste::find($request->input('list_id')));
        }

        if ($request->filled('meal_id')) {
            $action->for(Meal::find($request->input('meal_id')));
        }

        return new ItemResource(
            $action
                ->called($request->input('name'))
                ->from(Item::find($request->input('item_id')))
                ->perform()
        );
    }

    public function update(Item $item, UpdateItemRequest $request, UpdateItem $action): ItemResource
    {
        if ($request->filled('list_id')) {
            $action->for(Liste::find($request->input('list_id')), 'lists');
        }

        if ($request->filled('meal_id')) {
            $action->for(Meal::find($request->input('meal_id')), 'meals');
        }

        return new ItemResource(
            $action->perform($item, $request->input('name'))
        );
    }

    public function destroy(Item $item, DeleteItem $action): Response
    {
        if (request()->filled('list_id')) {
            $action->from(Liste::find(request()->input('list_id')));
        }

        if (request()->filled('meal_id')) {
            $action->from(Meal::find(request()->input('meal_id')));
        }

        $action->perform($item);

        return response()->noContent();
    }
}
