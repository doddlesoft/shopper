<?php

namespace Tests\Unit\Actions\Meals;

use App\Actions\Meals\DeleteMeal;
use App\Item;
use App\Itemable;
use App\Liste;
use App\Meal;
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

    /** @test */
    public function the_meal_items_are_also_deleted()
    {
        $item = factory(Item::class)->create(['name' => 'Test Meal Item']);
        $meal = factory(Meal::class)->create(['name' => 'Test Meal']);
        $meal->items()->attach($item);

        app(DeleteMeal::class)->perform($meal);

        $this->assertDatabaseMissing('meals', ['name' => 'Test Meal']);
        $this->assertEquals(0, Meal::count());
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertEquals(0, Itemable::count());
        $this->assertDatabaseHas('items', ['name' => 'Test Meal Item']);
        $this->assertEquals(1, Item::count());
    }

    /** @test */
    public function the_shopping_list_meals_are_also_detached()
    {
        $item = factory(Item::class)->create(['name' => 'Test Meal Item']);
        $meal = factory(Meal::class)->create(['name' => 'Test Meal']);
        $meal->items()->attach($item);
        $list = factory(Liste::class)->create(['name' => 'Test Shopping List']);
        $list->items()->attach($item);
        $list->meals()->attach($meal);

        app(DeleteMeal::class)->perform($meal);

        $this->assertDatabaseMissing('meals', ['name' => 'Test Meal']);
        $this->assertEquals(0, Meal::count());
        $this->assertDatabaseMissing('list_meal', ['list_id' => $list->id, 'meal_id' => $meal->id]);
    }
}
