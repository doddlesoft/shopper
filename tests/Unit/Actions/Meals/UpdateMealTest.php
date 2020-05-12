<?php

namespace Tests\Unit\Actions\Meals;

use App\Actions\Meals\UpdateMeal;
use App\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateMealTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_meal_is_updated()
    {
        $meal = factory(Meal::class)->create(['name' => 'Test Meal']);

        app(UpdateMeal::class)->perform($meal, 'Updated Meal');

        $this->assertDatabaseMissing('meals', ['name' => 'Test Meal']);
        $this->assertDatabaseHas('meals', ['name' => 'Updated Meal']);
    }
}
