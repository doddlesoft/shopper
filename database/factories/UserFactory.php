<?php

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});

$factory->afterCreatingState(User::class, 'with_token', function ($user) {
    $user->tokens()->create([
        'name' => $user->name.' Test Token',
        'token' => hash('sha256', 'token'.$user->id), // token1, token2 ...
        'abilities' => ['*'],
    ]);
});
