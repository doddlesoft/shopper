<?php

namespace App;

class Liste extends Model
{
    protected $table = 'lists';

    public function items()
    {
        return $this
            ->morphToMany(Item::class, 'itemable')
            ->withPivot('completed_at')
            ->withTimestamps();
    }

    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'list_meal', 'list_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
