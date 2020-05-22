<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Meal extends JsonResource
{
    public function toArray($request)
    {
        $includes = explode(',', $request->query('include'));

        return array_merge(parent::toArray($request), [
            'items' => $this->when(in_array('items', $includes), new ItemCollection($this->items)),
            'lists' => $this->when(in_array('lists', $includes), new ListCollection($this->lists)),
        ]);
    }
}
