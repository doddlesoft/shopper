<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Liste extends JsonResource
{
    public function toArray($request)
    {
        $includes = explode(',', $request->query('include'));

        return array_merge([
            'items' => $this->when(in_array('items', $includes), new ItemCollection($this->items)),
            'meals' => $this->when(in_array('meals', $includes), new MealCollection($this->meals)),
        ], parent::toArray($request));
    }
}
