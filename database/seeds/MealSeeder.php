<?php

use App\Meal;
use App\User;
use Illuminate\Database\Seeder;

class MealSeeder extends Seeder
{
    public function run()
    {
        User::all()->each(function ($user) {
            factory(Meal::class, 2)
                ->states(['with_items'])
                ->create(['user_id' => $user->id]);
        });
    }
}
