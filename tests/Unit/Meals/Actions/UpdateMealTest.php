<?php

namespace Tests\Unit\Meals\Actions;

use App\Meal;
use App\Meals\Actions\UpdateMeal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateMealTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_meal_is_updated()
    {
        $meal = factory(Meal::class)->create(['name' => 'New Meal']);

        app(UpdateMeal::class)->perform($meal, 'Updated Meal');

        $this->assertDatabaseMissing('meals', ['name' => 'New Meal']);
        $this->assertDatabaseHas('meals', ['name' => 'Updated Meal']);
    }
}
