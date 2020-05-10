<?php

namespace Tests\Unit\Items\Actions;

use App\Item;
use App\Items\Actions\UpdateItem;
use App\Liste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_item_is_updated()
    {
        $item = factory(Item::class)->create(['name' => 'New Shopping List Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);

        app(UpdateItem::class)
            ->forList($list)
            ->perform($item, 'Updated Shopping List Item');

        $this->assertDatabaseMissing('items', ['name' => 'New Shopping List Item']);
        $this->assertDatabaseHas('items', ['name' => 'Updated Shopping List Item']);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('item_list', ['item_id' => $item->id, 'list_id' => $list->id]);
        $this->assertEquals(1, $list->items->count());
    }

    /** @test */
    public function updating_an_item_used_on_another_list_creates_a_new_item_to_preserve_the_original_item()
    {
        $item = factory(Item::class)->create(['name' => 'First Shopping List Item']);
        $list1 = factory(Liste::class)->create();
        $list1->items()->attach($item);
        $list2 = factory(Liste::class)->create();
        $list2->items()->attach($item);

        $item2 = app(UpdateItem::class)
            ->forList($list2)
            ->perform($item, 'Second Shopping List Item');

        $this->assertDatabaseHas('items', ['name' => 'First Shopping List Item']);
        $this->assertDatabaseHas('items', ['name' => 'Second Shopping List Item']);
        $this->assertEquals(2, Item::count());
        $this->assertDatabaseHas('item_list', ['item_id' => $item->id, 'list_id' => $list1->id]);
        $this->assertEquals(1, $list1->items->count());
        $this->assertDatabaseHas('item_list', ['item_id' => $item2->id, 'list_id' => $list2->id]);
        $this->assertEquals(1, $list2->items->count());
    }

    /** @test */
    public function updating_an_item_used_on_another_list_with_an_item_thats_also_on_another_list_preserves_both_items_and_doesnt_create_a_new_item()
    {
        $item1 = factory(Item::class)->create(['name' => 'First Shopping List Item']);
        $item2 = factory(Item::class)->create(['name' => 'Second Shopping List Item']);
        $list1 = factory(Liste::class)->create();
        $list1->items()->attach($item1);
        $list2 = factory(Liste::class)->create();
        $list2->items()->attach($item1);

        $item2 = app(UpdateItem::class)
            ->forList($list2)
            ->perform($item1, 'Second Shopping List Item');

        $this->assertDatabaseHas('items', ['name' => 'First Shopping List Item']);
        $this->assertDatabaseHas('items', ['name' => 'Second Shopping List Item']);
        $this->assertEquals(2, Item::count());
        $this->assertDatabaseHas('item_list', ['item_id' => $item1->id, 'list_id' => $list1->id]);
        $this->assertEquals(1, $list1->items->count());
        $this->assertDatabaseHas('item_list', ['item_id' => $item2->id, 'list_id' => $list2->id]);
        $this->assertEquals(1, $list2->items->count());
    }
}
