<?php

use App\Item;
use App\Liste;
use App\Meal;
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

$factory->afterCreatingState(Liste::class, 'with_meals', function ($list) {
    $meal1 = factory(Meal::class)->states(['with_items'])->create(['user_id' => $list->user_id]);
    $meal2 = factory(Meal::class)->states(['with_items'])->create(['user_id' => $list->user_id]);

    $list->meals()->attach([$meal1->id, $meal2->id], ['created_at' => now(), 'updated_at' => now()]);
    $list->items()->saveMany($meal1->items->merge($meal2->items));
});
