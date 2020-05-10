<?php

namespace App\Items\Actions;

use App\Item;
use App\Items\Actions\CreateItem;
use App\Liste;

class UpdateItem
{
    private $list;

    public function perform(Item $item, string $name): Item
    {
        if (! $item->existsOnAnotherList($this->list)) {
            return tap($item)->update(['name' => $name]);
        }

        $this->list->items()->detach($item);

        return app(CreateItem::class)
            ->called($name)
            ->forList($this->list)
            ->perform();
    }

    public function forList(Liste $list)
    {
        $this->list = $list;

        return $this;
    }
}
