<?php

use App\Liste;
use Illuminate\Database\Seeder;

class ListSeeder extends Seeder
{
    public function run()
    {
        factory(Liste::class, 10)->states(['with_items'])->create();
    }
}
