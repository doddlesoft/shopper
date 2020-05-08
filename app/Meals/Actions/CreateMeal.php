<?php

namespace App\Meals\Actions;

use App\Meal;

class CreateMeal
{
    public function perform(string $name): Meal
    {
        return Meal::create(['name' => $name]);
    }
}
