<?php

namespace Tests\Unit;

use App\Item;
use App\Liste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_an_item_doesnt_exist_on_another_list()
    {
        $item = factory(Item::class)->create();
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);

        $this->assertFalse($item->existsOnAnotherList($list));
    }

    /** @test */
    public function when_an_item_does_exist_on_another_list()
    {
        $item = factory(Item::class)->create();
        $list1 = factory(Liste::class)->create();
        $list1->items()->attach($item);
        $list2 = factory(Liste::class)->create();
        $list2->items()->attach($item);

        $this->assertTrue($item->existsOnAnotherList($list1));
        $this->assertTrue($item->existsOnAnotherList($list2));
    }
}
