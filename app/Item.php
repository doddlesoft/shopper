<?php

namespace App;

class Item extends Model
{
    public function lists()
    {
        return $this->morphedByMany(Liste::class, 'itemable');
    }

    public function existsOnAnotherList($notThisList)
    {
        return $this->lists->where('id', '<>', $notThisList->id)->count() > 0;
    }
}
