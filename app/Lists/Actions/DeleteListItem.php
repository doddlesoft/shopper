<?php

namespace App\Lists\Actions;

use App\Item;
use App\Liste;

class DeleteListItem
{
    public function perform(Liste $list, Item $item)
    {
        $list->items()->detach($item);

        if (! $item->existsOnAnotherList($list)) {
            $item->delete();
        }
    }
}
