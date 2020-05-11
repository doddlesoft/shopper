<?php

namespace Tests\Unit;

use App\Item;
use App\Liste;
use App\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_an_item_isnt_used_elsewhere()
    {
        $item = factory(Item::class)->create();
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);

        $this->assertFalse($item->usedElsewhere($list->id, 'lists'));
    }

    /** @test */
    public function when_an_item_is_used_elsewhere()
    {
        $item = factory(Item::class)->create();
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);
        $meal = factory(Meal::class)->create();
        $meal->items()->attach($item);

        $this->assertTrue($item->usedElsewhere($list->id, 'lists'));
        $this->assertTrue($item->usedElsewhere($meal->id, 'meals'));
    }
}
