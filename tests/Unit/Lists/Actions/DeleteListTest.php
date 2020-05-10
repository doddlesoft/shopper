<?php

namespace Tests\Unit\Lists\Actions;

use App\Item;
use App\Itemable;
use App\Liste;
use App\Lists\Actions\DeleteList;
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
        $item1 = factory(Item::class)->create(['name' => 'Test Shopping List Item']);
        $item2 = factory(Item::class)->create(['name' => 'Test Shopping List Item']);
        $list1 = factory(Liste::class)->create(['name' => 'First Shopping List']);
        $list1->items()->attach($item1);
        $list2 = factory(Liste::class)->create(['name' => 'Second Shopping List']);
        $list2->items()->attach($item1);
        $list2->items()->attach($item2);

        app(DeleteList::class)->perform($list2);

        $this->assertDatabaseHas('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('lists', ['name' => 'First Shopping List']);
        $this->assertDatabaseMissing('lists', ['name' => 'Second Shopping List']);
        $this->assertEquals(1, Liste::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item1->id,
            'itemable_id' => $list1->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, $list1->items->count());
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item1->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item2->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, Itemable::count());
    }
}
