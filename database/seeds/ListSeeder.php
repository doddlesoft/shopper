<?php

use App\Liste;
use App\User;
use Illuminate\Database\Seeder;

class ListSeeder extends Seeder
{
    public function run()
    {
        User::all()->each(function ($user) {
            factory(Liste::class, 2)
                ->states(['with_items', 'with_meals'])
                ->create(['user_id' => $user->id]);
        });
    }
}
