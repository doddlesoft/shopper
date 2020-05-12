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

        $this->assertDatabaseMissing('items', ['name' => 'Test Meal Item']);
        $this->assertEquals(0, Item::count());
        $this->assertDatabaseMissing('meals', ['name' => 'Test Meal']);
        $this->assertEquals(0, Meal::count());
        $this->assertEquals(0, Itemable::count());
    }

    /** @test */
    public function deleting_a_meal_that_contains_an_item_used_elsewhere_only_detaches_that_item_from_the_specified_list()
    {
        $item1 = factory(Item::class)->create(['name' => 'First Item']);
        $item2 = factory(Item::class)->create(['name' => 'Second Item']);
        $meal = factory(Meal::class)->create(['name' => 'Meal']);
        $meal->items()->attach($item1);
        $meal->items()->attach($item2);
        $list = factory(Liste::class)->create(['name' => 'Shopping List']);
        $list->items()->attach($item1);

        app(DeleteMeal::class)->perform($meal);

        $this->assertDatabaseHas('items', ['name' => 'First Item']);
        $this->assertDatabaseMissing('items', ['name' => 'Second Item']);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseMissing('meals', ['name' => 'Meal']);
        $this->assertEquals(0, Meal::count());
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item1->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item2->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertDatabaseHas('lists', ['name' => 'Shopping List']);
        $this->assertEquals(1, Liste::count());
        $this->assertEquals(1, $list->items->count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item1->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, Itemable::count());
    }
}
