<?php

use App\Item;
use App\Meal;
use Faker\Generator as Faker;

$factory->define(Meal::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->afterCreatingState(Meal::class, 'with_items', function ($list) {
    $list->items()->save(factory(Item::class)->create());
    $list->items()->save(factory(Item::class)->create());
});
