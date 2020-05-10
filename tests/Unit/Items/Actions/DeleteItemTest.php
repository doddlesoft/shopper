<?php

namespace Tests\Unit\Items\Actions;

use App\Item;
use App\Itemable;
use App\Items\Actions\DeleteItem;
use App\Liste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_item_is_deleted()
    {
        $item = factory(Item::class)->create(['name' => 'Test Shopping List Item']);

        app(DeleteItem::class)->perform($item);

        $this->assertDatabaseMissing('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(0, Item::count());
    }

    /** @test */
    public function the_shopping_list_item_is_deleted()
    {
        $item = factory(Item::class)->create(['name' => 'Test Shopping List Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);

        app(DeleteItem::class)
            ->fromList($list)
            ->perform($item);

        $this->assertDatabaseMissing('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(0, Item::count());
        $this->assertEquals(0, $list->items->count());
    }

    /** @test */
    public function deleting_an_item_used_on_another_list_only_detaches_the_item_from_the_specified_list()
    {
        $item = factory(Item::class)->create(['name' => 'Test Shopping List Item']);
        $list1 = factory(Liste::class)->create();
        $list1->items()->attach($item);
        $list2 = factory(Liste::class)->create();
        $list2->items()->attach($item);

        app(DeleteItem::class)
            ->fromList($list2)
            ->perform($item);

        $this->assertDatabaseHas('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $list1->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, $list1->items->count());
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(0, $list2->items->count());
        $this->assertEquals(1, Itemable::count());
    }
}
