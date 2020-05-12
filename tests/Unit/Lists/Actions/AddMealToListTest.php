<?php

namespace Tests\Unit\Lists\Actions;

use App\Item;
use App\Itemable;
use App\Liste;
use App\Lists\Actions\AddMealToList;
use App\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddMealToListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_meal_is_added_to_the_list()
    {
        $list = factory(Liste::class)->create();
        $meal = factory(Meal::class)->create();
        $item1 = factory(Item::class)->create();
        $item2 = factory(Item::class)->create();
        $meal->items()->attach([$item1->id, $item2->id]);
        $this->assertEquals(2, Itemable::count());

        app(AddMealToList::class)->perform($list, $meal);

        $this->assertDatabaseHas('list_meal', [
            'list_id' => $list->id,
            'meal_id' => $meal->id,
        ]);
        $this->assertEquals(4, Itemable::count());
        $this->assertEquals(1, $list->meals->count());
        $this->assertEquals(2, $list->items->count());
        $this->assertEquals(1, $meal->lists->count());
    }
}
