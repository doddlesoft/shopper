<?php

namespace App\Actions\Lists;

use App\Actions\Items\CreateItem;
use App\Liste;
use App\Meal;

class CreateList
{
    private $list;
    private $newList;
    private $onlyIncomplete = false;

    public function perform(string $name): Liste
    {
        $this->newList = Liste::create([
            'user_id' => auth()->id(),
            'name' => $name,
        ]);

        if ($this->list !== null) {
            $this->copyItems();
            $this->copyMeals();
        }

        return $this->newList;
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

    private function copyItems()
    {
        $this
            ->list
            ->items
            ->when($this->onlyIncomplete, function ($items) {
                return $items->whereNull('pivot.completed_at');
            })
            ->each(function ($item) {
                app(CreateItem::class)
                    ->from($item)
                    ->for($this->newList)
                    ->perform();
            });
    }

    private function copyMeals()
    {
        $this
            ->list
            ->meals
            ->when($this->onlyIncomplete, function ($meals) {
                return $meals->filter(function ($meal) {
                    return $this->mealIsRequiredOnNewList($meal);
                });
            })
            ->each(function ($meal) {
                $this->newList->meals()->attach($meal);
            });
    }

    private function mealIsRequiredOnNewList(Meal $meal)
    {
        return $this->newList->items->pluck('id')->intersect($meal->items->pluck('id'))->count() > 0;
    }
}
