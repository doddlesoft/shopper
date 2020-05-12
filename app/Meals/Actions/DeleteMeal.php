<?php

namespace App\Meals\Actions;

use App\Items\Actions\DeleteItem;
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

        $meal->delete();
    }
}
