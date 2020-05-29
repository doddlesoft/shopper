<?php

namespace Tests\Unit\Actions\Items;

use App\Actions\Items\IncompleteItem;
use App\Item;
use App\Liste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncompleteItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_item_is_incompleted()
    {
        $item = factory(Item::class)->create();
        $list = factory(Liste::class)->create();
        $list->items()->attach($item, ['completed_at' => now()]);
        $this->assertNotNull($list->items->where('id', $item->id)->first()->pivot->completed_at);

        app(IncompleteItem::class)->perform($item, $list);

        $this->assertNull($list->fresh()->items->where('id', $item->id)->first()->pivot->completed_at);
    }

    /** @test */
    public function incompleting_an_item_only_completes_it_on_the_specified_list()
    {
        $item = factory(Item::class)->create();
        $list1 = factory(Liste::class)->create();
        $list1->items()->attach($item, ['completed_at' => now()]);
        $list2 = factory(Liste::class)->create();
        $list2->items()->attach($item, ['completed_at' => now()]);

        app(IncompleteItem::class)->perform($item, $list1);

        $this->assertNull($list1->fresh()->items->where('id', $item->id)->first()->pivot->completed_at);
        $this->assertNotNull($list2->fresh()->items->where('id', $item->id)->first()->pivot->completed_at);
    }
}
