<?php

namespace App\Actions\Items;

use App\Item;
use App\Model;

class UpdateItem
{
    private $itemable;
    private $itemableType;

    public function perform(Item $item, string $name): Item
    {
        if ($this->itemable !== null) {
            return $this->updateForItemable($item, $name);
        }

        if ($item->itemables->count() > 0) {
            return app(CreateItem::class)
                ->called($name)
                ->perform();
        }

        return tap($item)->update(['name' => $name]);
    }

    public function for(Model $itemable, string $itemableType): self
    {
        $this->itemable = $itemable;
        $this->itemableType = $itemableType;

        return $this;
    }

    private function updateForItemable(Item $item, string $name)
    {
        if (! $item->usedElsewhere($this->itemable->id, $this->itemableType)) {
            return tap($item)->update(['name' => $name]);
        }

        $this->itemable->items()->detach($item);

        return app(CreateItem::class)
            ->called($name)
            ->for($this->itemable)
            ->perform();
    }
}
