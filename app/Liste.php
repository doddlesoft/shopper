<?php

namespace App;

class Liste extends Model
{
    protected $table = 'lists';

    public function items()
    {
        return $this->morphToMany(Item::class, 'itemable');
    }
}
