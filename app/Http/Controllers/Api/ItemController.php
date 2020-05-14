<?php

namespace App\Http\Controllers\Api;

use App\Actions\Items\CreateItem;
use App\Actions\Items\DeleteItem;
use App\Actions\Items\UpdateItem;
use App\Http\Requests\CreateItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Resources\Item as ItemResource;
use App\Http\Resources\ItemCollection;
use App\Item;
use App\Liste;
use App\Meal;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ItemController
{
    public function index(): ItemCollection
    {
        $sortColumn = request()->query('sort', 'created_at');
        $sortDirection = Str::startsWith($sortColumn, '-') ? 'desc' : 'asc';
        $sortColumn = ltrim($sortColumn, '-');

        $query = Item::query()
            ->select(['items.*'])
            ->when(request()->filled('list_id'), function ($query) {
                $query->forItemable(request()->query('list_id'), 'lists');
            })
            ->when(request()->filled('meal_id'), function ($query) {
                $query->forItemable(request()->query('meal_id'), 'meals');
            })
            ->when($sortColumn === 'meal', function ($query) use ($sortDirection) {
                $query->orderByMealName($sortDirection);
            }, function ($query) use ($sortColumn, $sortDirection) {
                $query->orderBy($sortColumn, $sortDirection);
            });

        return new ItemCollection($query->get());
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
