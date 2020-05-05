<?php

use App\Liste;
use Faker\Generator as Faker;

$factory->define(Liste::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});
