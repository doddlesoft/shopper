<?php

namespace App\Lists\Actions;

use App\Item;
use App\Liste;

class UpdateListItem
{
    public function perform(Liste $list, Item $item, string $name): Item
    {
        if (! $item->existsOnAnotherList($list)) {
            return tap($item)->update(['name' => $name]);
        }

        $list->items()->detach($item);

        return app(CreateListItem::class)
            ->itemName($name)
            ->perform($list);
    }
}
