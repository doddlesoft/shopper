<?php

namespace App;

class Item extends Model
{
    public function lists()
    {
        return $this->morphedByMany(Liste::class, 'itemable');
    }

    public function meals()
    {
        return $this->morphedByMany(Meal::class, 'itemable');
    }

    public function itemables()
    {
        return $this->hasMany(Itemable::class);
    }

    public function usedElsewhere(string $itemableId, string $itemableType)
    {
        return $this
            ->itemables
            ->reject(function ($itemable) use ($itemableId, $itemableType) {
                return $itemable->itemable_id === $itemableId
                    && $itemable->itemable_type === $itemableType;
            })
            ->count() > 0;
    }
}
