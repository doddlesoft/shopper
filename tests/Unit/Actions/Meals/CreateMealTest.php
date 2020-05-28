<?php

namespace Tests\Unit\Actions\Meals;

use App\Actions\Meals\CreateMeal;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateMealTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_meal_is_created()
    {
        Sanctum::actingAs(
            $user = factory(User::class)->create(),
            ['*'],
        );

        app(CreateMeal::class)->perform('Test Meal');

        $this->assertDatabaseHas('meals', [
            'user_id' => $user->id,
            'name' => 'Test Meal',
        ]);
    }
}
