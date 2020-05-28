<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Item;
use App\Itemable;
use App\Liste;
use App\Meal;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function getting_all_shopping_lists()
    {
        $list1 = factory(Liste::class)->create();
        $list2 = factory(Liste::class)->create();

        Sanctum::actingAs($list1->user, ['*']);

        $response = $this->getJson(route('lists.index'));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $list1->id,
                        'name' => $list1->name,
                    ],
                ],
            ])
            ->assertJsonMissing([
                'id' => $list2->id,
                'name' => $list2->name,
            ]);
    }

    /** @test */
    public function getting_a_shopping_list()
    {
        $list = factory(Liste::class)->create(['name' => 'Test Shopping List']);

        Sanctum::actingAs($list->user, ['*']);

        $response = $this->getJson(route('lists.show', $list));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $list->id,
                    'name' => $list->name,
                ],
            ]);
    }

    /** @test */
    public function getting_a_shopping_list_that_isnt_the_logged_in_users_returns_a_403()
    {
        $list1 = factory(Liste::class)->create();
        $list2 = factory(Liste::class)->create();

        Sanctum::actingAs($list1->user, ['*']);

        $response = $this->getJson(route('lists.show', $list2));

        $response->assertStatus(403);
    }

    /** @test */
    public function getting_a_shopping_list_inluding_its_items_and_meals()
    {
        $user = factory(User::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $list = factory(Liste::class)->create(['user_id' => $user->id]);
        $list->items()->attach([$item1->id, $item2->id]);
        $meal1 = factory(Meal::class)->create(['user_id' => $user->id]);
        $meal2 = factory(Meal::class)->create(['user_id' => $user->id]);
        $list->meals()->attach([$meal1->id, $meal2->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('lists.show', ['list' => $list, 'include' => 'items,meals']));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $list->id,
                    'name' => $list->name,
                    'items' => [
                        [
                            'id' => $item1->id,
                            'name' => $item1->name,
                        ],
                        [
                            'id' => $item2->id,
                            'name' => $item2->name,
                        ],
                    ],
                    'meals' => [
                        [
                            'id' => $meal1->id,
                            'name' => $meal1->name,
                        ],
                        [
                            'id' => $meal2->id,
                            'name' => $meal2->name,
                        ],
                    ],
                ],
            ]);
    }

    /** @test */
    public function creating_a_shopping_list()
    {
        Sanctum::actingAs(
            $user = factory(User::class)->create(),
            ['*'],
        );

        $response = $this->postJson(route('lists.store'), ['name' => 'Test Shopping List']);

        $response
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'user_id' => $user->id,
                    'name' => 'Test Shopping List',
                ],
            ]);

        $this->assertDatabaseHas('lists', [
            'user_id' => $user->id,
            'name' => 'Test Shopping List',
        ]);
    }

    /** @test */
    public function creating_a_shopping_list_from_another_shopping_list()
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
        $this->assertEquals(2, Itemable::count());

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson(route('lists.store'), [
            'list_id' => $list->id,
            'name' => 'Second Shopping List',
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'user_id' => $user->id,
                    'name' => 'Second Shopping List',
                ],
            ]);

        $this->assertDatabaseHas('lists', [
            'user_id' => $user->id,
            'name' => 'First Shopping List',
        ]);
        $this->assertEquals(2, $list->items->count());
        $this->assertDatabaseHas('lists', [
            'user_id' => $user->id,
            'name' => 'Second Shopping List',
        ]);
        $this->assertEquals(4, Itemable::count());
    }

    /** @test */
    public function creating_a_shopping_list_from_another_shopping_list_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $list = factory(Liste::class)->create([
            'user_id' => $user1->id,
            'name' => 'First Shopping List',
        ]);
        $item1 = factory(Item::class)->create([
            'user_id' => $user1->id,
            'name' => 'First Item',
        ]);
        $item2 = factory(Item::class)->create([
            'user_id' => $user1->id,
            'name' => 'Second Item',
        ]);
        $list->items()->attach([$item1->id, $item2->id]);
        $this->assertEquals(2, Itemable::count());

        Sanctum::actingAs(factory(User::class)->create(), ['*']);

        $response = $this->postJson(route('lists.store'), [
            'list_id' => $list->id,
            'name' => 'Second Shopping List',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function creating_a_shopping_list_from_another_shopping_list_with_only_incomplete_items()
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
        $this->assertEquals(2, Itemable::count());

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson(route('lists.store'), [
            'list_id' => $list->id,
            'name' => 'Second Shopping List',
            'only_incomplete' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'user_id' => $user->id,
                    'name' => 'Second Shopping List',
                ],
            ]);

        $this->assertDatabaseHas('lists', [
            'user_id' => $user->id,
            'name' => 'First Shopping List',
        ]);
        $this->assertEquals(2, $list->items->count());
        $this->assertDatabaseHas('lists', [
            'user_id' => $user->id,
            'name' => 'Second Shopping List',
        ]);
        $this->assertEquals(3, Itemable::count());
    }

    /** @test */
    public function updating_a_shopping_list()
    {
        $user = factory(User::class)->create();
        $list = factory(Liste::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Shopping List',
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->patchJson(route('lists.update', $list), ['name' => 'Updated Shopping List']);

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $list->id,
                    'name' => 'Updated Shopping List',
                ],
            ]);

        $this->assertDatabaseMissing('lists', ['name' => 'Test Shopping List']);
        $this->assertDatabaseHas('lists', ['name' => 'Updated Shopping List']);
    }

    /** @test */
    public function updating_a_shopping_list_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $list = factory(Liste::class)->create([
            'user_id' => $user1->id,
            'name' => 'Test Shopping List',
        ]);

        Sanctum::actingAs($user2, ['*']);

        $response = $this->patchJson(route('lists.update', $list), ['name' => 'Updated Shopping List']);

        $response->assertStatus(403);

        $this->assertDatabaseHas('lists', ['name' => 'Test Shopping List']);
        $this->assertDatabaseMissing('lists', ['name' => 'Updated Shopping List']);
    }

    /** @test */
    public function deleting_a_shopping_list()
    {
        $user = factory(User::class)->create();
        $list = factory(Liste::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Shopping List',
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->deleteJson(route('lists.destroy', $list));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('lists', ['name' => 'Test Shopping List']);
        $this->assertEquals(0, Liste::count());
    }

    /** @test */
    public function deleting_a_shopping_list_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $list = factory(Liste::class)->create([
            'user_id' => $user1->id,
            'name' => 'Test Shopping List',
        ]);

        Sanctum::actingAs($user2, ['*']);

        $response = $this->deleteJson(route('lists.destroy', $list));

        $response->assertStatus(403);

        $this->assertDatabaseHas('lists', ['name' => 'Test Shopping List']);
        $this->assertEquals(1, Liste::count());
    }

    /**
     * @test
     * @dataProvider nameInputValidation
     * @dataProvider onlyIncompleteInputValidation
     */
    public function test_store_form_validation($formInput, $formInputValue)
    {
        Sanctum::actingAs(factory(User::class)->create(), ['*']);

        $response = $this->postJson(route('lists.store'), [$formInput => $formInputValue]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    /**
     * @test
     * @dataProvider nameInputValidation
     */
    public function test_update_form_validation($formInput, $formInputValue)
    {
        $user = factory(User::class)->create();
        $list = factory(Liste::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Shopping List',
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->patchJson(route('lists.update', $list), [$formInput => $formInputValue]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    public function nameInputValidation()
    {
        return [
            'Name is required' => ['name', ''],
            'Name is no longer than 250 characters' => ['name', Str::random(251)],
        ];
    }

    public function onlyIncompleteInputValidation()
    {
        return [
            'Only Incomplete must be a boolean' => ['only_incomplete', 'String'],
        ];
    }
}
