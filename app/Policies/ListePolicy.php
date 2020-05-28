<?php

namespace App\Policies;

use App\Liste;
use App\Meal;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ListePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Liste $list)
    {
        return $user->id === (int) $list->user_id;
    }

    public function create(User $user)
    {
        if (request()->filled('list_id')) {
            return $user->can('view', Liste::find(request()->input('list_id')));
        }

        return true;
    }

    public function update(User $user, Liste $list)
    {
        return $user->id === (int) $list->user_id;
    }

    public function delete(User $user, Liste $list)
    {
        return $user->id === (int) $list->user_id;
    }

    public function addMeal(User $user)
    {
        return request()->filled('meal_id') &&
            $user->id === (int) Meal::find(request()->input('meal_id'))->user_id;
    }
}
