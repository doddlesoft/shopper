<?php

use App\Item;
use App\Liste;
use App\User;
use Faker\Generator as Faker;

$factory->define(Liste::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'name' => $faker->sentence(2),
    ];
});

$factory->afterCreatingState(Liste::class, 'with_items', function ($list) {
    $list->items()->saveMany([
        factory(Item::class)->create(['user_id' => $list->user_id]),
        factory(Item::class)->create(['user_id' => $list->user_id]),
    ]);
});
