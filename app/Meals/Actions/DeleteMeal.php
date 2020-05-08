<?php

namespace App\Meals\Actions;

use App\Meal;

class DeleteMeal
{
    public function perform(Meal $meal)
    {
        $meal->delete();
    }
}
