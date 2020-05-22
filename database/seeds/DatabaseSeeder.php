<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(ItemSeeder::class);
        $this->call(ListSeeder::class);
        $this->call(MealSeeder::class);
    }
}
