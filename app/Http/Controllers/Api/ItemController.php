<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Resources\Item as ItemResource;
use App\Item;
use App\Items\Actions\CreateItem;
use App\Items\Actions\DeleteItem;
use App\Items\Actions\UpdateItem;
use App\Liste;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ItemController
{
    public function index(): AnonymousResourceCollection
    {
        if (request()->filled('list_id')) {
            return ItemResource::collection(Liste::findOrFail(request()->query('list_id'))->items);
        }

        return ItemResource::collection(Item::all());
    }

    public function store(CreateItemRequest $request, CreateItem $action): ItemResource
    {
        return new ItemResource(
            $action
                ->called($request->input('name'))
                ->from(Item::find($request->input('item_id')))
                ->forList(Liste::find($request->input('list_id')))
                ->perform()
        );
    }

    public function update(Item $item, UpdateItemRequest $request, UpdateItem $action): ItemResource
    {
        return new ItemResource(
            $action
                ->forList(Liste::find($request->input('list_id')))
                ->perform($item, $request->input('name'))
        );
    }

    public function destroy(Item $item, DeleteItem $action): Response
    {
        $action
            ->fromList(Liste::find(request()->input('list_id')))
            ->perform($item);

        return response()->noContent();
    }
}
