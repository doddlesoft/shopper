<?php

namespace App\Lists\Actions;

use App\Items\Actions\DeleteItem;
use App\Liste;

class DeleteList
{
    public function perform(Liste $list)
    {
        $list->items->each(function ($item) use ($list) {
            app(DeleteItem::class)
                ->fromList($list)
                ->perform($item);
        });

        $list->delete();
    }
}
