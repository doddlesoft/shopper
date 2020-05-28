<?php

namespace Tests\Unit\Actions\Lists;

use App\Actions\Lists\CreateList;
use App\Item;
use App\Liste;
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
    public function creating_a_shopping_list_from_another_list_with_all_items()
    {
        $user = factory(User::class)->create();
        $list = factory(Liste::class)->create([
            'user_id' => $user->id,
            'name' => 'First Shopping List',
        ]);
        $item1 = factory(Item::class)->create([
            'user_id' => $user->id,
            'name' => 'First Item',
        ]);
        $item2 = factory(Item::class)->create([
            'user_id' => $user->id,
            'name' => 'Second Item',
        ]);
        $list->items()->attach([$item1->id, $item2->id]);

        Sanctum::actingAs($user, ['*']);

        $list2 = app(CreateList::class)
            ->from($list)
            ->perform('Second Shopping List');

        $this->assertDatabaseHas('lists', [
            'user_id' => $user->id,
            'name' => 'Second Shopping List',
        ]);
        $this->assertEquals(2, $list2->items->count());
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
    }

    /** @test */
    public function creating_a_shopping_list_from_another_list_with_only_incomplete_items()
    {
        $user = factory(User::class)->create();
        $list = factory(Liste::class)->create([
            'user_id' => $user->id,
            'name' => 'First Shopping List',
        ]);
        $item1 = factory(Item::class)->create([
            'user_id' => $user->id,
            'name' => 'First Item',
        ]);
        $item2 = factory(Item::class)->create([
            'user_id' => $user->id,
            'name' => 'Second Item',
        ]);
        $list->items()->attach($item1, ['completed_at' => now()]);
        $list->items()->attach($item2);

        Sanctum::actingAs($user, ['*']);

        $list2 = app(CreateList::class)
            ->from($list)
            ->onlyIncomplete()
            ->perform('Second Shopping List');

        $this->assertDatabaseHas('lists', [
            'user_id' => $user->id,
            'name' => 'Second Shopping List',
        ]);
        $this->assertEquals(1, $list2->items->count());
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
    }
}
