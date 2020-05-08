<?php

use App\Meal;
use Faker\Generator as Faker;

$factory->define(Meal::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});
