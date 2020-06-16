<?php

namespace App\Actions\Lists;

use App\Actions\Items\DeleteItem;
use App\Liste;
use App\Meal;

class DeleteMealFromList
{
    public function perform(Liste $list, Meal $meal): void
    {
        $meal->items->each(function ($item) use ($list) {
            app(DeleteItem::class)
                ->from($list)
                ->perform($item);
        });

        $list->meals()->detach($meal);
    }
}
