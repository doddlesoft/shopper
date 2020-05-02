<?php

namespace App\Http\Controllers\Api;

use App\Liste;
use App\Lists\Actions\CreateList;
use Illuminate\Http\Request;

class ListController
{
    public function store(Request $request, CreateList $action): Liste
    {
        return $action->perform($request->input('name'));
    }
}
