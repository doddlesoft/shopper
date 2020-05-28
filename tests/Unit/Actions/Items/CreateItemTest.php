<?php

namespace Tests\Unit\Actions\Items;

use App\Actions\Items\CreateItem;
use App\Item;
use App\Liste;
use App\Meal;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_new_item_is_created()
    {
        Sanctum::actingAs(
            $user = factory(User::class)->create(),
            ['*'],
        );

        app(CreateItem::class)
            ->called('Test Item')
            ->perform();

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'Test Item',
        ]);
        $this->assertEquals(1, Item::count());
    }

    /** @test */
    public function creating_an_item_for_a_shopping_list()
    {
        $user = factory(User::class)->create();
        $list = factory(Liste::class)->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $item = app(CreateItem::class)
            ->called('Test Shopping List Item')
            ->for($list)
            ->perform();

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'Test Shopping List Item',
        ]);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, $list->items->count());
    }

    /** @test */
    public function creating_an_item_for_a_meal()
    {
        $user = factory(User::class)->create();
        $meal = factory(Meal::class)->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $item = app(CreateItem::class)
            ->called('Test Meal Item')
            ->for($meal)
            ->perform();

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'Test Meal Item',
        ]);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertEquals(1, $meal->items->count());
    }

    /** @test */
    public function adding_an_existing_item_to_a_shopping_list()
    {
        $user = factory(User::class)->create();
        $item = factory(Item::class)->create(['user_id' => $user->id]);
        $list = factory(Liste::class)->create(['user_id' => $user->id]);

        app(CreateItem::class)
            ->from($item)
            ->for($list)
            ->perform();

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => $item->name,
        ]);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $list->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertEquals(1, $list->items->count());
    }

    /** @test */
    public function adding_an_existing_item_to_a_meal()
    {
        $user = factory(User::class)->create();
        $item = factory(Item::class)->create(['user_id' => $user->id]);
        $meal = factory(Meal::class)->create(['user_id' => $user->id]);

        app(CreateItem::class)
            ->from($item)
            ->for($meal)
            ->perform();

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => $item->name,
        ]);
        $this->assertEquals(1, Item::count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item->id,
            'itemable_id' => $meal->id,
            'itemable_type' => 'meals',
        ]);
        $this->assertEquals(1, $meal->items->count());
    }
}
