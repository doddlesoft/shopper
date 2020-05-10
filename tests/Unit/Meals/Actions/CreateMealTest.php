<?php

namespace Tests\Unit\Meals\Actions;

use App\Meals\Actions\CreateMeal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateMealTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_meal_is_created()
    {
        app(CreateMeal::class)->perform('Test Meal');

        $this->assertDatabaseHas('meals', ['name' => 'Test Meal']);
    }
}
