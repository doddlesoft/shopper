<?php

namespace App\Actions\Items;

use App\Item;
use App\Model;

class CreateItem
{
    private $name;
    private $item;
    private $itemable;

    public function perform(): Item
    {
        $item = $this->item ?? Item::firstOrCreate(['name' => $this->name]);

        if ($this->itemable !== null) {
            $this->itemable->items()->attach($item);
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

    public function for(Model $itemable): self
    {
        $this->itemable = $itemable;

        return $this;
    }
}
