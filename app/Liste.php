<?php

namespace App;

class Liste extends Model
{
    protected $table = 'lists';

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_list', 'list_id');
    }
}
