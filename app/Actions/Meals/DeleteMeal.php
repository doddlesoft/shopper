<?php

namespace App\Actions\Meals;

use App\Actions\Items\DeleteItem;
use App\Meal;

class DeleteMeal
{
    public function perform(Meal $meal)
    {
        $meal->items->each(function ($item) use ($meal) {
            app(DeleteItem::class)
                ->from($meal)
                ->perform($item);
        });

        $meal->lists()->detach();
        $meal->delete();
    }
}
