<?php

namespace Tests\Unit\Items\Actions;

use App\Item;
use App\Itemable;
use App\Items\Actions\DeleteItem;
use App\Liste;
use App\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_item_is_deleted()
    {
        $item = factory(Item::class)->create(['name' => 'Test Item']);

        app(DeleteItem::class)->perform($item);

        $this->assertDatabaseMissing('items', ['name' => 'Test Item']);
        $this->assertEquals(0, Item::count());
    }

    /** @test */
    public function deleting_an_item_being_used_detaches_it_from_all_itemables_before_deleting_it()
    {
        $item = factory(Item::class)->create(['name' => 'Test Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);
        $meal = factory(Meal::class)->create();
        $meal->items()->attach($item);

        app(DeleteItem::class)->perform($item);

        $this->assertDatabaseMissing('items', ['name' => 'Test Item']);
        $this->assertEquals(0, Item::count());
        $this->assertEquals(0, $list->items->count());
        $this->assertEquals(0, $meal->items->count());
    }

    /** @test */
    public function deleting_an_item_from_a_shopping_list()
    {
        $item = factory(Item::class)->create(['name' => 'Test Shopping List Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);

        app(DeleteItem::class)
            ->from($list)
            ->perform($item);

        $this->assertDatabaseMissing('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(0, Item::count());
        $this->assertEquals(0, $list->items->count());
    }

    /** @test */
    public function deleting_an_item_from_a_meal()
    {
        $item = factory(Item::class)->create(['name' => 'Test Meal Item']);
        $meal = factory(Meal::class)->create();
        $meal->items()->attach($item);

        app(DeleteItem::class)
            ->from($meal)
            ->perform($item);

        $this->assertDatabaseMissing('items', ['name' => 'Test Meal Item']);
        $this->assertEquals(0, Item::count());
        $this->assertEquals(0, $meal->items->count());
    }

    /** @test */
    public function deleting_an_item_from_a_shopping_list_used_elsewhere_only_detaches_the_item_from_the_shopping_list()
    {
        $item = factory(Item::class)->create(['name' => 'Test Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);
        $meal = factory(Meal::class)->create();
        $meal->items()->attach($item);

        app(DeleteItem::class)
            ->from($list)
            ->perform($item);

        $this->assertDatabaseHas('items', ['name' => 'Test Item']);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(0, $list->items->count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertEquals(1, $meal->items->count());
        $this->assertEquals(1, Itemable::count());
    }

    /** @test */
    public function deleting_an_item_from_a_meal_used_elsewhere_only_detaches_the_item_from_the_meal()
    {
        $item = factory(Item::class)->create(['name' => 'Test Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);
        $meal = factory(Meal::class)->create();
        $meal->items()->attach($item);

        app(DeleteItem::class)
            ->from($meal)
            ->perform($item);

        $this->assertDatabaseHas('items', ['name' => 'Test Item']);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, $list->items->count());
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertEquals(0, $meal->items->count());
        $this->assertEquals(1, Itemable::count());
    }
}
