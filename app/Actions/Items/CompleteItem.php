<?php

namespace App\Actions\Items;

use App\Item;
use App\Liste;

class CompleteItem
{
    public function perform(Item $item, Liste $list): void
    {
        $list->items()->updateExistingPivot($item->id, ['completed_at' => now()]);
    }
}
