<?php

namespace Tests\Unit\Items\Actions;

use App\Item;
use App\Items\Actions\UpdateItem;
use App\Liste;
use App\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_item_is_updated()
    {
        $item = factory(Item::class)->create(['name' => 'Test Item']);

        app(UpdateItem::class)->perform($item, 'Updated Item');

        $this->assertDatabaseMissing('items', ['name' => 'Test Item']);
        $this->assertDatabaseHas('items', ['name' => 'Updated Item']);
        $this->assertEquals(1, Item::count());
    }

    /** @test */
    public function updating_an_item_used_elsewhere_creates_a_new_item_to_preserve_the_original_item()
    {
        $item1 = factory(Item::class)->create(['name' => 'First Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item1);

        app(UpdateItem::class)->perform($item1, 'Second Item');

        $this->assertDatabaseHas('items', ['name' => 'First Item']);
        $this->assertDatabaseHas('items', ['name' => 'Second Item']);
        $this->assertEquals(2, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item1->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, $list->items->count());
    }

    /** @test */
    public function updating_an_item_for_a_shopping_list()
    {
        $item = factory(Item::class)->create(['name' => 'Test Shopping List Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);

        app(UpdateItem::class)
            ->for($list, 'lists')
            ->perform($item, 'Updated Shopping List Item');

        $this->assertDatabaseMissing('items', ['name' => 'Test Shopping List Item']);
        $this->assertDatabaseHas('items', ['name' => 'Updated Shopping List Item']);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, $list->items->count());
    }

    /** @test */
    public function updating_an_item_for_a_meal()
    {
        $item = factory(Item::class)->create(['name' => 'Test Meal Item']);
        $meal = factory(Meal::class)->create();
        $meal->items()->attach($item);

        app(UpdateItem::class)
            ->for($meal, 'meals')
            ->perform($item, 'Updated Meal Item');

        $this->assertDatabaseMissing('items', ['name' => 'Test Meal Item']);
        $this->assertDatabaseHas('items', ['name' => 'Updated Meal Item']);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertEquals(1, $meal->items->count());
    }

    /** @test */
    public function updating_an_item_for_a_shopping_list_used_elsewhere_creates_a_new_item_to_preserve_the_original_item()
    {
        $item1 = factory(Item::class)->create(['name' => 'First Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item1);
        $meal = factory(Meal::class)->create();
        $meal->items()->attach($item1);

        $item2 = app(UpdateItem::class)
            ->for($list, 'lists')
            ->perform($item1, 'Second Item');

        $this->assertDatabaseHas('items', ['name' => 'First Item']);
        $this->assertDatabaseHas('items', ['name' => 'Second Item']);
        $this->assertEquals(2, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item2->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, $list->items->count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item1->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertEquals(1, $meal->items->count());
    }

    /** @test */
    public function updating_an_item_for_a_meal_used_elsewhere_creates_a_new_item_to_preserve_the_original_item()
    {
        $item1 = factory(Item::class)->create(['name' => 'First Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item1);
        $meal = factory(Meal::class)->create();
        $meal->items()->attach($item1);

        $item2 = app(UpdateItem::class)
            ->for($meal, 'meals')
            ->perform($item1, 'Second Item');

        $this->assertDatabaseHas('items', ['name' => 'First Item']);
        $this->assertDatabaseHas('items', ['name' => 'Second Item']);
        $this->assertEquals(2, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item1->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, $list->items->count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item2->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertEquals(1, $meal->items->count());
    }

    /** @test */
    public function updating_an_item_for_a_shopping_list_used_elsewhere_with_an_item_thats_also_used_elsewhere_preserves_both_items_and_doesnt_create_a_new_item()
    {
        $item1 = factory(Item::class)->create(['name' => 'First Item']);
        $item2 = factory(Item::class)->create(['name' => 'Second Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item1);
        $meal = factory(Meal::class)->create();
        $meal->items()->attach($item1);

        $item2 = app(UpdateItem::class)
            ->for($list, 'lists')
            ->perform($item1, 'Second Item');

        $this->assertDatabaseHas('items', ['name' => 'First Item']);
        $this->assertDatabaseHas('items', ['name' => 'Second Item']);
        $this->assertEquals(2, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item2->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, $list->items->count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item1->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertEquals(1, $meal->items->count());
    }

    /** @test */
    public function updating_an_item_for_a_meal_used_elsewhere_with_an_item_thats_also_used_elsewhere_preserves_both_items_and_doesnt_create_a_new_item()
    {
        $item1 = factory(Item::class)->create(['name' => 'First Item']);
        $item2 = factory(Item::class)->create(['name' => 'Second Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item1);
        $meal = factory(Meal::class)->create();
        $meal->items()->attach($item1);

        $item2 = app(UpdateItem::class)
            ->for($meal, 'meals')
            ->perform($item1, 'Second Item');

        $this->assertDatabaseHas('items', ['name' => 'First Item']);
        $this->assertDatabaseHas('items', ['name' => 'Second Item']);
        $this->assertEquals(2, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item1->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, $list->items->count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item2->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertEquals(1, $meal->items->count());
    }
}
