<?php

namespace App\Actions\Lists;

use App\Actions\Items\DeleteItem;
use App\Liste;

class DeleteList
{
    public function perform(Liste $list)
    {
        $list->items->each(function ($item) use ($list) {
            app(DeleteItem::class)
                ->from($list)
                ->perform($item);
        });

        $list->meals()->detach();
        $list->delete();
    }
}
