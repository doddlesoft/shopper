<?php

namespace App\Items\Actions;

use App\Item;
use App\Liste;

class DeleteItem
{
    private $list;

    public function perform(Item $item)
    {
        if ($this->list !== null) {
            $this->list->items()->detach($item);
        }

        if ($item->lists->count() === 0) {
            $item->delete();
        }
    }

    public function fromList(Liste $list): self
    {
        $this->list = $list;

        return $this;
    }
}
