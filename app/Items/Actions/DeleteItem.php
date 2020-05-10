<?php

namespace App\Items\Actions;

use App\Item;
use App\Liste;

class DeleteItem
{
    private $list;

    public function perform(Item $item)
    {
        $this->list->items()->detach($item);

        if (! $item->existsOnAnotherList($this->list)) {
            $item->delete();
        }
    }

    public function fromList(Liste $list): self
    {
        $this->list = $list;

        return $this;
    }
}
