<?php

namespace Tests\Unit\Actions\Lists;

use App\Actions\Lists\DeleteMealFromList;
use App\Item;
use App\Itemable;
use App\Liste;
use App\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteMealFromListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_meal_is_deleted_from_the_list()
    {
        $item1 = factory(Item::class)->create();
        $item2 = factory(Item::class)->create();
        $meal = factory(Meal::class)->create();
        $meal->items()->attach([$item1->id, $item2->id]);
        $list = factory(Liste::class)->create();
        $list->items()->attach([$item1->id, $item2->id]);
        $list->meals()->attach($meal);
        $this->assertEquals(4, Itemable::count());

        app(DeleteMealFromList::class)->perform($list, $meal);

        $this->assertDatabaseMissing('list_meal', [
            'list_id' => $list->id,
            'meal_id' => $meal->id,
        ]);
        $this->assertEquals(2, Itemable::count());
        $this->assertEquals(0, $list->items->count());
        $this->assertEquals(0, $list->meals->count());
        $this->assertEquals(0, $meal->lists->count());
    }
}
