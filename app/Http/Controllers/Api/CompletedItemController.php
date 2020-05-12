<?php

namespace App\Http\Controllers\Api;

use App\Actions\Items\CompleteItem;
use App\Http\Requests\CompleteItemRequest;
use App\Item;
use App\Liste;
use Illuminate\Http\Response;

class CompletedItemController
{
    public function store(CompleteItemRequest $request, CompleteItem $action): Response
    {
        $action->perform(
            Item::find($request->input('item_id')),
            Liste::find($request->input('list_id'))
        );

        return response()->noContent();
    }
}
