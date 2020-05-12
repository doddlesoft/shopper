<?php

namespace App\Actions\Items;

use App\Item;
use App\Model;

class DeleteItem
{
    private $itemable;

    public function perform(Item $item): void
    {
        if ($this->itemable !== null) {
            $this->itemable->items()->detach($item);
        }

        if ($this->itemable === null) {
            $item->itemables()->delete();
        }

        if ($item->itemables->count() === 0) {
            $item->delete();
        }
    }

    public function from(Model $itemable): self
    {
        $this->itemable = $itemable;

        return $this;
    }
}
