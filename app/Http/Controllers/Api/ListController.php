<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ListRequest;
use App\Http\Resources\Liste as ListResource;
use App\Liste;
use App\Lists\Actions\CreateList;
use App\Lists\Actions\DeleteList;
use App\Lists\Actions\UpdateList;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ListController
{
    public function index(): AnonymousResourceCollection
    {
        return ListResource::collection(Liste::all());
    }

    public function store(ListRequest $request, CreateList $action): ListResource
    {
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
