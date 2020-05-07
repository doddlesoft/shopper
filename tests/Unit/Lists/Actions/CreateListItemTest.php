<?php

namespace Tests\Unit\Lists\Actions;

use App\Item;
use App\Liste;
use App\Lists\Actions\CreateListItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateListItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_new_shopping_list_item_is_created()
    {
        $list = factory(Liste::class)->create();

        $item = app(CreateListItem::class)
            ->itemName('Test Shopping List Item')
            ->perform($list);

        $this->assertDatabaseHas('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('item_list', ['item_id' => $item->id, 'list_id' => $list->id]);
        $this->assertEquals(1, $list->items->count());
    }

    /** @test */
    public function adding_an_existing_item_to_a_shopping_list_doesnt_create_a_new_item()
    {
        $item = factory(Item::class)->create();
        $list = factory(Liste::class)->create();

        app(CreateListItem::class)
            ->itemId($item->id)
            ->perform($list);

        $this->assertDatabaseHas('items', ['name' => $item->name]);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('item_list', ['item_id' => $item->id, 'list_id' => $list->id]);
        $this->assertEquals(1, $list->items->count());
    }
}
