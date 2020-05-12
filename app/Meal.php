<?php

namespace App;

class Meal extends Model
{
    public function items()
    {
        return $this->morphToMany(Item::class, 'itemable');
    }

    public function lists()
    {
        return $this->belongsToMany(Liste::class, 'list_meal', 'meal_id', 'list_id');
    }
}
