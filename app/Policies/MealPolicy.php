<?php

namespace App\Policies;

use App\Meal;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MealPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Meal $meal)
    {
        return $user->id === (int) $meal->user_id;
    }

    public function update(User $user, Meal $meal)
    {
        return $user->id === (int) $meal->user_id;
    }

    public function delete(User $user, Meal $meal)
    {
        return $user->id === (int) $meal->user_id;
    }
}
