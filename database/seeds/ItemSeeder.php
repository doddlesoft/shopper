<?php

use App\Item;
use App\User;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run()
    {
        User::all()->each(function ($user) {
            factory(Item::class, 2)->create(['user_id' => $user->id]);
        });
    }
}
