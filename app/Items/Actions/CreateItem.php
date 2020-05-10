<?php

namespace App\Items\Actions;

use App\Item;
use App\Liste;

class CreateItem
{
    private $name;
    private $list;
    private $item;

    public function perform(): Item
    {
        $item = $this->item ?? Item::firstOrCreate(['name' => $this->name]);

        if ($this->list !== null) {
            $this->list->items()->attach($item);
        }

        return $item;
    }

    public function called(?string $name = null): self
    {
        $this->name = $name;

        return $this;
    }

    public function from(?Item $item = null): self
    {
        $this->item = $item;

        return $this;
    }

    public function forList(?Liste $list = null): self
    {
        $this->list = $list;

        return $this;
    }
}
