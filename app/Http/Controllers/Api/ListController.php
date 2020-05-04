<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ListRequest;
use App\Liste;
use App\Lists\Actions\CreateList;
use App\Lists\Actions\UpdateList;

class ListController
{
    public function store(ListRequest $request, CreateList $action): Liste
    {
        return $action->perform($request->input('name'));
    }

    public function update(Liste $list, ListRequest $request, UpdateList $action)
    {
        return $action->perform($list, $request->input('name'));
    }
}
