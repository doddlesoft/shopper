<?php

namespace Tests\Unit\Actions\Items;

use App\Actions\Items\CompleteItem;
use App\Item;
use App\Liste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompleteItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_item_is_completed()
    {
        $item = factory(Item::class)->create();
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);

        app(CompleteItem::class)->perform($item, $list);

        $this->assertNotNull($list->items->where('id', $item->id)->first()->pivot->completed_at);
    }

    /** @test */
    public function completing_an_item_only_completes_it_on_the_specified_list()
    {
        $item = factory(Item::class)->create();
        $list1 = factory(Liste::class)->create();
        $list1->items()->attach($item);
        $list2 = factory(Liste::class)->create();
        $list2->items()->attach($item);

        app(CompleteItem::class)->perform($item, $list1);

        $this->assertNotNull($list1->items->where('id', $item->id)->first()->pivot->completed_at);
        $this->assertNull($list2->items->where('id', $item->id)->first()->pivot->completed_at);
    }
}
