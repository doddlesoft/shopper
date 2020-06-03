<?php

namespace App\Http\Controllers\Api;

use App\Actions\Items\CompleteItem;
use App\Actions\Items\IncompleteItem;
use App\Http\Requests\CompleteItemRequest;
use App\Http\Requests\IncompleteItemRequest;
use App\Item;
use App\Liste;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;

class CompletedItemController
{
    use AuthorizesRequests;

    public function store(CompleteItemRequest $request, CompleteItem $action): Response
    {
        $this->authorize('complete', Item::class);

        $action->perform(
            Item::find($request->input('item_id')),
            Liste::find($request->input('list_id')),
        );

        return response()->noContent();
    }

    public function destroy(Item $item, IncompleteItemRequest $request, IncompleteItem $action): Response
    {
        $this->authorize('incomplete', $item);

        $action->perform($item, Liste::find($request->input('list_id')));

        return response()->noContent();
    }
}
