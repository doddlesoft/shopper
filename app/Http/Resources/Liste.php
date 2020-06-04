<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Liste extends JsonResource
{
    public function toArray($request)
    {
        $includes = explode(',', $request->query('include'));

        return array_merge(parent::toArray($request), [
            'items' => $this->when(in_array('items', $includes) && $this->items->count() > 0, new ItemCollection($this->items)),
            'meals' => $this->when(in_array('meals', $includes) && $this->meals->count() > 0, new MealCollection($this->meals)),
        ]);
    }
}
