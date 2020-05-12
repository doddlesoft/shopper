<?php

namespace App\Actions\Lists;

use App\Actions\Items\CreateItem;
use App\Liste;
use App\Meal;

class AddMealToList
{
    public function perform(Liste $list, Meal $meal): void
    {
        $list->meals()->attach($meal);

        $meal->items->each(function ($item) use ($list) {
            app(CreateItem::class)
                ->from($item)
                ->for($list)
                ->perform();
        });
    }
}
