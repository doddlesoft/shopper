<?php

namespace Tests\Unit\Items\Actions;

use App\Item;
use App\Items\Actions\CreateItem;
use App\Liste;
use App\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_new_item_is_created()
    {
        app(CreateItem::class)
            ->called('Test Item')
            ->perform();

        $this->assertDatabaseHas('items', ['name' => 'Test Item']);
        $this->assertEquals(1, Item::count());
    }

    /** @test */
    public function creating_an_item_for_a_shopping_list()
    {
        $list = factory(Liste::class)->create();

        $item = app(CreateItem::class)
            ->called('Test Shopping List Item')
            ->for($list)
            ->perform();

        $this->assertDatabaseHas('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, $list->items->count());
    }

    /** @test */
    public function creating_an_item_for_a_meal()
    {
        $meal = factory(Meal::class)->create();

        $item = app(CreateItem::class)
            ->called('Test Meal Item')
            ->for($meal)
            ->perform();

        $this->assertDatabaseHas('items', ['name' => 'Test Meal Item']);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertEquals(1, $meal->items->count());
    }

    /** @test */
    public function adding_an_existing_item_to_a_shopping_list()
    {
        $item = factory(Item::class)->create();
        $list = factory(Liste::class)->create();

        app(CreateItem::class)
            ->from($item)
            ->for($list)
            ->perform();

        $this->assertDatabaseHas('items', ['name' => $item->name]);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, $list->items->count());
    }

    /** @test */
    public function adding_an_existing_item_to_a_meal()
    {
        $item = factory(Item::class)->create();
        $meal = factory(Meal::class)->create();

        app(CreateItem::class)
            ->from($item)
            ->for($meal)
            ->perform();

        $this->assertDatabaseHas('items', ['name' => $item->name]);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertEquals(1, $meal->items->count());
    }
}
