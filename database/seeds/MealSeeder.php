<?php

use App\Meal;
use Illuminate\Database\Seeder;

class MealSeeder extends Seeder
{
    public function run()
    {
        factory(Meal::class, 10)->states(['with_items'])->create();
    }
}
