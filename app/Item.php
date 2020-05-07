<?php

namespace App;

class Item extends Model
{
    public function lists()
    {
        return $this->belongsToMany(Liste::class, 'item_list', 'item_id', 'list_id');
    }

    public function existsOnAnotherList($notThisList)
    {
        return $this->lists->where('id', '<>', $notThisList->id)->count() > 0;
    }
}
