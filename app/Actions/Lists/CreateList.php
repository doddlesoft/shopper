<?php

namespace App\Actions\Lists;

use App\Actions\Items\CreateItem;
use App\Liste;

class CreateList
{
    private $list;
    private $onlyIncomplete = false;

    public function perform(string $name): Liste
    {
        $list = Liste::create(['name' => $name]);

        if ($this->list !== null) {
            $this->copyItems($list);
        }

        return $list;
    }

    public function from(Liste $list): self
    {
        $this->list = $list;

        return $this;
    }

    public function onlyIncomplete(): self
    {
        $this->onlyIncomplete = true;

        return $this;
    }

    private function copyItems($list)
    {
        $this
            ->list
            ->items
            ->when($this->onlyIncomplete, function ($items) {
                return $items->whereNull('pivot.completed_at');
            })
            ->each(function ($item) use ($list) {
                app(CreateItem::class)
                    ->from($item)
                    ->for($list)
                    ->perform();
            });
    }
}
