<?php

namespace Tests\Unit\Actions\Lists;

use App\Actions\Lists\CreateList;
use App\Item;
use App\Liste;
use App\Meal;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_shopping_list_is_created()
    {
        Sanctum::actingAs(
            $user = factory(User::class)->create(),
            ['*'],
        );

        app(CreateList::class)->perform('Test Shopping List');

        $this->assertDatabaseHas('lists', [
            'user_id' => $user->id,
            'name' => 'Test Shopping List',
        ]);
    }

    /** @test */
    public function creating_a_shopping_list_from_another_list_with_all_items_and_meals()
    {
        $user = factory(User::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id, 'name' => 'First Item']);
        $item2 = factory(Item::class)->create(['user_id' => $user->id, 'name' => 'Second Item']);
        $list = factory(Liste::class)->create(['user_id' => $user->id, 'name' => 'First Shopping List']);
        $list->items()->attach([$item1->id, $item2->id]);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $item4 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal1 = factory(Meal::class)->create(['user_id' => $user->id]);
        $meal1->items()->attach([$item3->id, $item4->id]);
        $list->meals()->attach($meal1);
        $list->items()->attach([$item3->id, $item4->id]);
        $item5 = factory(Item::class)->create(['user_id' => $user->id]);
        $item6 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal2 = factory(Meal::class)->create(['user_id' => $user->id]);
        $meal2->items()->attach([$item5->id, $item6->id]);
        $list->meals()->attach($meal2);
        $list->items()->attach([$item5->id, $item6->id]);

        Sanctum::actingAs($user, ['*']);

        $list2 = app(CreateList::class)
            ->from($list)
            ->perform('Second Shopping List');

        $this->assertDatabaseHas('lists', [
            'user_id' => $user->id,
            'name' => 'Second Shopping List',
        ]);
        $this->assertEquals(6, $list2->items->count());
        $this->assertEquals(2, $list2->meals->count());
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item1->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item2->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item3->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item4->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item5->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item6->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseHas('list_meal', [
            'list_id' => $list2->id,
            'meal_id' => $meal1->id,
        ]);
        $this->assertDatabaseHas('list_meal', [
            'list_id' => $list2->id,
            'meal_id' => $meal2->id,
        ]);
    }

    /** @test */
    public function creating_a_shopping_list_from_another_list_with_only_incomplete_items_and_meal()
    {
        $user = factory(User::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id, 'name' => 'First Item']);
        $item2 = factory(Item::class)->create(['user_id' => $user->id, 'name' => 'Second Item']);
        $list = factory(Liste::class)->create(['user_id' => $user->id, 'name' => 'First Shopping List']);
        $list->items()->attach($item1, ['completed_at' => now()]);
        $list->items()->attach($item2);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $item4 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal1 = factory(Meal::class)->create(['user_id' => $user->id]);
        $meal1->items()->attach([$item3->id, $item4->id]);
        $list->meals()->attach($meal1);
        $list->items()->attach([$item3->id, $item4->id], ['completed_at' => now()]);
        $item5 = factory(Item::class)->create(['user_id' => $user->id]);
        $item6 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal2 = factory(Meal::class)->create(['user_id' => $user->id]);
        $meal2->items()->attach([$item5->id, $item6->id]);
        $list->meals()->attach($meal2);
        $list->items()->attach($item5);
        $list->items()->attach($item6, ['completed_at' => now()]);

        Sanctum::actingAs($user, ['*']);

        $list2 = app(CreateList::class)
            ->from($list)
            ->onlyIncomplete()
            ->perform('Second Shopping List');

        $this->assertDatabaseHas('lists', [
            'user_id' => $user->id,
            'name' => 'Second Shopping List',
        ]);
        $this->assertEquals(2, $list2->items->count());
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item1->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item2->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item3->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item4->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseHas('itemables', [
            'item_id' => $item5->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseMissing('itemables', [
            'item_id' => $item6->id,
            'itemable_id' => $list2->id,
            'itemable_type' => 'lists',
        ]);
        $this->assertDatabaseMissing('list_meal', [
            'list_id' => $list2->id,
            'meal_id' => $meal1->id,
        ]);
        $this->assertDatabaseHas('list_meal', [
            'list_id' => $list2->id,
            'meal_id' => $meal2->id,
        ]);
    }
}
