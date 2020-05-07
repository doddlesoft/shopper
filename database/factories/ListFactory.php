<?php

use App\Item;
use App\Liste;
use Faker\Generator as Faker;

$factory->define(Liste::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->afterCreatingState(Liste::class, 'with_items', function ($list) {
    $list->items()->save(factory(Item::class)->create());
    $list->items()->save(factory(Item::class)->create());
});
