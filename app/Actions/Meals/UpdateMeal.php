<?php

namespace App\Actions\Meals;

use App\Meal;

class UpdateMeal
{
    public function perform(Meal $meal, string $name): Meal
    {
        return tap($meal)->update(['name' => $name]);
    }
}
