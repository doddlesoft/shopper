<?php

namespace App;

class Meal extends Model
{
    public function items()
    {
        return $this->morphToMany(Item::class, 'itemable');
    }
}
