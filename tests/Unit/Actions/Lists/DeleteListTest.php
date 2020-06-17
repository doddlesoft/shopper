<?php

namespace Tests\Unit\Actions\Lists;

use App\Actions\Lists\DeleteList;
use App\Item;
use App\Itemable;
use App\Liste;
use App\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_shopping_list_is_deleted()
    {
        $list = factory(Liste::class)->create(['name' => 'Test Shopping List']);

        app(DeleteList::class)->perform($list);

        $this->assertDatabaseMissing('lists', ['name' => 'Test Shopping List']);
        $this->assertEquals(0, Liste::count());
    }

    /** @test */
    public function the_shopping_list_items_are_also_deleted()
    {
        $item = factory(Item::class)->create(['name' => 'Test Shopping List Item']);
        $list = factory(Liste::class)->create(['name' => 'Test Shopping List']);
        $list->items()->attach($item);

        app(DeleteList::class)->perform($list);

        $this->assertDatabaseMissing('lists', ['name' => 'Test Shopping List']);
        $this->assertEquals(0, Liste::count());
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(0, Itemable::count());
        $this->assertDatabaseHas('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(1, Item::count());
    }

    /** @test */
    public function the_shopping_list_meals_are_also_detached()
    {
        $item = factory(Item::class)->create(['name' => 'Test Shopping List Item']);
        $meal = factory(Meal::class)->create(['name' => 'Test Meal']);
        $meal->items()->attach($item);
        $list = factory(Liste::class)->create(['name' => 'Test Shopping List']);
        $list->items()->attach($item);
        $list->meals()->attach($meal);

        app(DeleteList::class)->perform($list);

        $this->assertDatabaseMissing('lists', ['name' => 'Test Shopping List']);
        $this->assertEquals(0, Liste::count());
        $this->assertDatabaseMissing('list_meal', ['list_id' => $list->id, 'meal_id' => $meal->id]);
    }
}
