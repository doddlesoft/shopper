<?php

namespace Tests\Unit\Items\Actions;

use App\Item;
use App\Items\Actions\CreateItem;
use App\Liste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_new_item_is_created()
    {
        app(CreateItem::class)
            ->called('Test Shopping List Item')
            ->perform();

        $this->assertDatabaseHas('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(1, Item::count());
    }

    /** @test */
    public function creating_an_item_for_a_shopping_list()
    {
        $list = factory(Liste::class)->create();

        $item = app(CreateItem::class)
            ->called('Test Shopping List Item')
            ->forList($list)
            ->perform();

        $this->assertDatabaseHas('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('item_list', ['item_id' => $item->id, 'list_id' => $list->id]);
        $this->assertEquals(1, $list->items->count());
    }

    /** @test */
    public function adding_an_existing_item_to_a_shopping_list()
    {
        $item = factory(Item::class)->create();
        $list = factory(Liste::class)->create();

        app(CreateItem::class)
            ->from($item)
            ->forList($list)
            ->perform();

        $this->assertDatabaseHas('items', ['name' => $item->name]);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('item_list', ['item_id' => $item->id, 'list_id' => $list->id]);
        $this->assertEquals(1, $list->items->count());
    }
}
