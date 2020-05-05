<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ListRequest;
use App\Liste;
use App\Lists\Actions\CreateList;
use App\Lists\Actions\DeleteList;
use App\Lists\Actions\UpdateList;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ListController
{
    public function index(): Collection
    {
        return Liste::all();
    }

    public function store(ListRequest $request, CreateList $action): Liste
    {
        return $action->perform($request->input('name'));
    }

    public function show(Liste $list): Liste
    {
        return $list;
    }

    public function update(Liste $list, ListRequest $request, UpdateList $action): Liste
    {
        return $action->perform($list, $request->input('name'));
    }

    public function destroy(Liste $list, DeleteList $action): Response
    {
        $action->perform($list);

        return response()->noContent();
    }
}
