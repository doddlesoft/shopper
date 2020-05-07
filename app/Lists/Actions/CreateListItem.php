<?php

namespace App\Lists\Actions;

use App\Item;
use App\Liste;

class CreateListItem
{
    private $itemId;
    private $itemName;

    public function perform(Liste $list): Item
    {
        $item = $this->getItem();

        $list->items()->attach($item->id);

        return $item;
    }

    public function itemId(?int $id = null): self
    {
        $this->itemId = $id;

        return $this;
    }

    public function itemName(?string $name = null): self
    {
        $this->itemName = $name;

        return $this;
    }

    private function getItem(): Item
    {
        if ($this->itemId !== null) {
            return Item::findOrFail($this->itemId);
        }

        return Item::firstOrCreate(['name' => $this->itemName]);
    }
}
