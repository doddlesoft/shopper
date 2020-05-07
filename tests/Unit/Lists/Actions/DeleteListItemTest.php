<?php

namespace Tests\Unit;

use App\Item;
use App\Liste;
use App\Lists\Actions\DeleteListItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteListItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_shopping_list_item_is_deleted()
    {
        $item = factory(Item::class)->create(['name' => 'Test Shopping List Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);

        app(DeleteListItem::class)->perform($list, $item);

        $this->assertDatabaseMissing('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(0, Item::count());
        $this->assertEquals(0, $list->items->count());
    }

    /** @test */
    public function deleting_an_item_used_on_another_list_only_removes_the_item_from_the_specified_list()
    {
        $item = factory(Item::class)->create(['name' => 'Test Shopping List Item']);
        $list1 = factory(Liste::class)->create();
        $list1->items()->attach($item);
        $list2 = factory(Liste::class)->create();
        $list2->items()->attach($item);

        app(DeleteListItem::class)->perform($list2, $item);

        $this->assertDatabaseHas('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('item_list', ['item_id' => $item->id, 'list_id' => $list1->id]);
        $this->assertEquals(1, $list1->items->count());
        $this->assertDatabaseMissing('item_list', ['item_id' => $item->id, 'list_id' => $list2->id]);
        $this->assertEquals(0, $list2->items->count());
    }
}
