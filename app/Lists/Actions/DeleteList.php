<?php

namespace App\Lists\Actions;

use App\Liste;

class DeleteList
{
    public function perform(Liste $list)
    {
        $list->items->each(function ($item) use ($list) {
            app(DeleteListItem::class)->perform($list, $item);
        });

        $list->delete();
    }
}
