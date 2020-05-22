<?php

namespace App;

class Meal extends Model
{
    public function items()
    {
        return $this
            ->morphToMany(Item::class, 'itemable')
            ->withPivot('completed_at')
            ->withTimestamps();
    }

    public function lists()
    {
        return $this->belongsToMany(Liste::class, 'list_meal', 'meal_id', 'list_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
