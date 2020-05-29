<?php

namespace App\Policies;

use App\Item;
use App\Liste;
use App\Meal;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        if (request()->filled('filter')) {
            [$criteria, $value] = explode(':', request()->query('filter'));

            if ($criteria === 'list') {
                return $user->can('view', Liste::find($value));
            }

            if ($criteria === 'meal') {
                return $user->can('view', Meal::find($value));
            }
        }

        return true;
    }

    public function create(User $user)
    {
        if (request()->filled('list_id')) {
            return $user->can('update', Liste::find(request()->input('list_id')));
        }

        if (request()->filled('meal_id')) {
            return $user->can('update', Meal::find(request()->input('meal_id')));
        }

        return true;
    }

    public function update(User $user, Item $item)
    {
        return $user->id === (int) $item->user_id;
    }

    public function delete(User $user, Item $item)
    {
        return $user->id === (int) $item->user_id;
    }

    public function complete(User $user)
    {
        $item =  Item::find(request()->input('item_id'));
        $list =  Liste::find(request()->input('list_id'));

        return $user->id === (int) $item->user_id &&
            $list->items->contains($item);
    }

    public function incomplete(User $user, Item $item)
    {;
        $list =  Liste::find(request()->input('list_id'));

        return $user->id === (int) $item->user_id &&
            $list->items->contains($item);
    }
}
