<?php

namespace App\Actions\Lists;

use App\Actions\Items\CreateItem;
use App\Liste;

class CreateList
{
    private $list;

    public function perform(string $name): Liste
    {
        $list = Liste::create(['name' => $name]);

        if ($this->list !== null) {
            $this->list->items->each(function ($item) use ($list) {
                app(CreateItem::class)
                    ->from($item)
                    ->for($list)
                    ->perform();
            });
        }

        return $list;
    }

    public function from(Liste $list): self
    {
        $this->list = $list;

        return $this;
    }
}
