<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateListItemRequest;
use App\Http\Requests\UpdateListItemRequest;
use App\Http\Resources\Item as ItemResource;
use App\Item;
use App\Liste;
use App\Lists\Actions\CreateListItem;
use App\Lists\Actions\DeleteListItem;
use App\Lists\Actions\UpdateListItem;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ListItemController
{
    public function index(Liste $list): AnonymousResourceCollection
    {
        return ItemResource::collection($list->items);
    }

    public function store(Liste $list, CreateListItemRequest $request, CreateListItem $action): ItemResource
    {
        return new ItemResource(
            $action
                ->itemId($request->input('item_id'))
                ->itemName($request->input('name'))
                ->perform($list)
        );
    }

    public function update(
        Liste $list,
        Item $item,
        UpdateListItemRequest $request,
        UpdateListItem $action
    ): ItemResource {
        return new ItemResource($action->perform($list, $item, $request->input('name')));
    }

    public function destroy(Liste $list, Item $item, DeleteListItem $action): Response
    {
        $action->perform($list, $item);

        return response()->noContent();
    }
}
