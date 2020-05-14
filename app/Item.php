<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;

class Item extends Model
{
    public function lists()
    {
        return $this->morphedByMany(Liste::class, 'itemable');
    }

    public function meals()
    {
        return $this->morphedByMany(Meal::class, 'itemable');
    }

    public function itemables()
    {
        return $this->hasMany(Itemable::class);
    }

    /* Scopes */
    public function scopeForItemable(Builder $query, int $itemableId, string $itemableType): Builder
    {
        return $query
            ->whereHas('itemables', function ($query) use ($itemableId, $itemableType) {
                $query
                    ->where('itemable_id', $itemableId)
                    ->where('itemable_type', $itemableType);
            });
    }

    public function scopeOrderByMealName(Builder $query, string $direction): Builder
    {
        return $query->addSelect('meals.name as meal_name')
            ->leftJoin('itemables', function ($join) {
                $join
                    ->on('itemables.item_id', 'items.id')
                    ->on('itemables.itemable_type', 'meals');
            })
            ->leftJoin('meals', function ($join) {
                $join
                    ->on('meals.id', 'itemables.itemable_id')
                    ->on('itemables.itemable_type', 'meals');
            })
            ->orderByRaw("meal_name is null, meal_name {$direction}");
    }

    /* Helpers */
    public function usedElsewhere(int $itemableId, string $itemableType): bool
    {
        return $this
            ->itemables
            ->reject(function ($itemable) use ($itemableId, $itemableType) {
                return (int) $itemable->itemable_id === $itemableId
                    && $itemable->itemable_type === $itemableType;
            })
            ->count() > 0;
    }
}
