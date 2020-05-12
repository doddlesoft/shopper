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

        $this->assertDatabaseMissing('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(0, Item::count());
        $this->assertDatabaseMissing('lists', ['name' => 'Test Shopping List']);
        $this->assertEquals(0, Liste::count());
        $this->assertEquals(0, Itemable::count());
    }

    /** @test */
    public function deleting_a_list_that_contains_an_item_used_on_another_list_only_detaches_that_item_from_the_specified_list()
    {
        $item1 = factory(Item::class)->create(['name' => 'First Item']);
        $item2 = factory(Item::class)->create(['name' => 'Second Item']);
        $list = factory(Liste::class)->create(['name' => 'Shopping List']);
        $list->items()->attach($item1);
        $list->items()->attach($item2);
        $meal = factory(Meal::class)->create(['name' => 'Meal']);
        $meal->items()->attach($item1);

        app(DeleteList::class)->perform($list);

        $this->assertDatabaseHas('items', ['name' => 'First Item']);
        $this->assertDatabaseMissing('items', ['name' => 'Second Item']);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseMissing('lists', ['name' => 'Shopping List']);
        $this->assertEquals(0, Liste::count());
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item1->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item2->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseHas('meals', ['name' => 'Meal']);
        $this->assertEquals(1, Meal::count());
        $this->assertEquals(1, $meal->items->count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item1->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertEquals(1, Itemable::count());
    }
}
