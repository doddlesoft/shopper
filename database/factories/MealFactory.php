<?php

use App\Item;
use App\Meal;
use App\User;
use Faker\Generator as Faker;

$factory->define(Meal::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'name' => $faker->sentence(2),
    ];
});

$factory->afterCreatingState(Meal::class, 'with_items', function ($meal) {
    $meal->items()->saveMany([
        factory(Item::class)->create(['user_id' => $meal->user_id]),
        factory(Item::class)->create(['user_id' => $meal->user_id]),
    ]);
});
