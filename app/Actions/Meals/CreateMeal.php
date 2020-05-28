<?php

namespace App\Actions\Meals;

use App\Meal;

class CreateMeal
{
    public function perform(string $name): Meal
    {
        return Meal::create([
            'user_id' => auth()->id(),
            'name' => $name,
        ]);
    }
}
