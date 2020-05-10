<?php

namespace Tests\Unit\Meals\Actions;

use App\Meal;
use App\Meals\Actions\DeleteMeal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteMealTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_meal_is_deleted()
    {
        $meal = factory(Meal::class)->create(['name' => 'Test Meal']);

        app(DeleteMeal::class)->perform($meal);

        $this->assertDatabaseMissing('meals', ['name' => 'Test Meal']);
        $this->assertEquals(0, Meal::count());
    }
}
