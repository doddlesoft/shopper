<?php

namespace App\Http\Controllers\Api;

use App\Actions\Lists\CreateList;
use App\Actions\Lists\DeleteList;
use App\Actions\Lists\UpdateList;
use App\Http\Requests\ListRequest;
use App\Http\Resources\ListCollection;
use App\Http\Resources\Liste as ListResource;
use App\Liste;
use Illuminate\Http\Response;

class ListController
{
    public function index(): ListCollection
    {
        return new ListCollection(request()->user()->lists);
    }

    public function store(ListRequest $request, CreateList $action): ListResource
    {
        if ($request->filled('list_id')) {
            $action->from(Liste::find($request->input('list_id')));
        }

        if ($request->boolean('only_incomplete')) {
            $action->onlyIncomplete();
        }

        return new ListResource($action->perform($request->input('name')));
    }

    public function show(Liste $list): ListResource
    {
        return new ListResource($list);
    }

    public function update(Liste $list, ListRequest $request, UpdateList $action): ListResource
    {
        return new ListResource($action->perform($list, $request->input('name')));
    }

    public function destroy(Liste $list, DeleteList $action): Response
    {
        $action->perform($list);

        return response()->noContent();
    }
}
