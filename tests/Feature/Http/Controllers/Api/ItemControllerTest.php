<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Item;
use App\Liste;
use App\Meal;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function getting_all_items()
    {
        $item1 = factory(Item::class)->create();
        $item2 = factory(Item::class)->create();

        Sanctum::actingAs($item1->user, ['*']);

        $response = $this->getJson(route('items.index'));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $item1->id,
                        'name' => $item1->name,
                    ],
                ],
            ])
            ->assertJsonMissing([
                'id' => $item2->id,
                'name' => $item2->name,
            ]);
    }

    /** @test */
    public function getting_paginated_items()
    {
        $user = factory(User::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $item4 = factory(Item::class)->create(['user_id' => $user->id]);
        $item5 = factory(Item::class)->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('items.index', ['page[number]' => 2, 'page[size]' => 2]));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $item3->id,
                        'name' => $item3->name,
                    ],
                    [
                        'id' => $item4->id,
                        'name' => $item4->name,
                    ],
                ],
            ])
            ->assertJsonMissing([
                'id' => $item1->id,
                'name' => $item1->name,
            ])
            ->assertJsonMissing([
                'id' => $item2->id,
                'name' => $item2->name,
            ])
            ->assertJsonMissing([
                'id' => $item5->id,
                'name' => $item5->name,
            ]);
    }

    /** @test */
    public function getting_all_items_ordered_by_date_created_asc()
    {
        $user = factory(User::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id, 'created_at' => now()]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id, 'created_at' => now()->subDay()]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('items.index', ['sort' => 'created_at']));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $item2->id,
                        'name' => $item2->name,
                    ],
                    [
                        'id' => $item1->id,
                        'name' => $item1->name,
                    ],
                ],
            ]);
    }

    /** @test */
    public function getting_all_items_ordered_by_date_created_desc()
    {
        $user = factory(User::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id, 'created_at' => now()->subDay()]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id, 'created_at' => now()]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('items.index', ['sort' => '-created_at']));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $item2->id,
                        'name' => $item2->name,
                    ],
                    [
                        'id' => $item1->id,
                        'name' => $item1->name,
                    ],
                ],
            ]);
    }

    /** @test */
    public function getting_all_items_ordered_by_name_asc()
    {
        $user = factory(User::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id, 'name' => 'Last Item']);
        $item2 = factory(Item::class)->create(['user_id' => $user->id, 'name' => 'First Item']);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('items.index', ['sort' => 'name']));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $item2->id,
                        'name' => $item2->name,
                    ],
                    [
                        'id' => $item1->id,
                        'name' => $item1->name,
                    ],
                ],
            ]);
    }

    /** @test */
    public function getting_all_items_ordered_by_name_desc()
    {
        $user = factory(User::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id, 'name' => 'First Item']);
        $item2 = factory(Item::class)->create(['user_id' => $user->id, 'name' => 'Last Item']);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('items.index', ['sort' => '-name']));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $item2->id,
                        'name' => $item2->name,
                    ],
                    [
                        'id' => $item1->id,
                        'name' => $item1->name,
                    ],
                ],
            ]);
    }

    /** @test */
    public function getting_all_items_ordered_by_meal_name_asc()
    {
        $user = factory(User::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal1 = factory(Meal::class)->create(['user_id' => $user->id, 'name' => 'Last Meal']);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $item4 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal1->items()->attach([$item3->id, $item4->id]);
        $meal2 = factory(Meal::class)->create(['user_id' => $user->id, 'name' => 'First Meal']);
        $item5 = factory(Item::class)->create(['user_id' => $user->id]);
        $item6 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal2->items()->attach([$item5->id, $item6->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('items.index', ['sort' => 'meal']));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $item5->id,
                        'name' => $item5->name,
                    ],
                    [
                        'id' => $item6->id,
                        'name' => $item6->name,
                    ],
                    [
                        'id' => $item3->id,
                        'name' => $item3->name,
                    ],
                    [
                        'id' => $item4->id,
                        'name' => $item4->name,
                    ],
                    [
                        'id' => $item1->id,
                        'name' => $item1->name,
                    ],
                    [
                        'id' => $item2->id,
                        'name' => $item2->name,
                    ],
                ],
            ]);
    }

    /** @test */
    public function getting_all_items_ordered_by_meal_name_desc()
    {
        $user = factory(User::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal1 = factory(Meal::class)->create(['user_id' => $user->id, 'name' => 'Last Meal']);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $item4 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal1->items()->attach([$item3->id, $item4->id]);
        $meal2 = factory(Meal::class)->create(['user_id' => $user->id, 'name' => 'First Meal']);
        $item5 = factory(Item::class)->create(['user_id' => $user->id]);
        $item6 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal2->items()->attach([$item5->id, $item6->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('items.index', ['sort' => '-meal']));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $item3->id,
                        'name' => $item3->name,
                    ],
                    [
                        'id' => $item4->id,
                        'name' => $item4->name,
                    ],
                    [
                        'id' => $item5->id,
                        'name' => $item5->name,
                    ],
                    [
                        'id' => $item6->id,
                        'name' => $item6->name,
                    ],
                    [
                        'id' => $item1->id,
                        'name' => $item1->name,
                    ],
                    [
                        'id' => $item2->id,
                        'name' => $item2->name,
                    ],
                ],
            ]);
    }

    /** @test */
    public function getting_all_shopping_list_items()
    {
        $list1 = factory(Liste::class)->states(['with_items'])->create();
        $list2 = factory(Liste::class)->states(['with_items'])->create();

        Sanctum::actingAs($list1->user, ['*']);

        $response = $this->getJson(route('items.index', ['filter' => 'list:'.$list1->id]));

        $response->assertOk();

        $list1->items->each(function ($item) use ($response) {
            $response->assertJsonFragment(['name' => $item->name]);
        });

        $list2->items->each(function ($item) use ($response) {
            $response->assertJsonMissing(['name' => $item->name]);
        });
    }

    /** @test */
    public function getting_all_shopping_list_items_ordered_by_meal_name_asc()
    {
        $user = factory(User::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $item4 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal1 = factory(Meal::class)->create(['user_id' => $user->id, 'name' => 'Last Meal']);
        $item5 = factory(Item::class)->create(['user_id' => $user->id]);
        $item6 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal1->items()->attach([$item5->id, $item6->id]);
        $meal2 = factory(Meal::class)->create(['user_id' => $user->id, 'name' => 'First Meal']);
        $item7 = factory(Item::class)->create(['user_id' => $user->id]);
        $item8 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal2->items()->attach([$item7->id, $item8->id]);
        $list = factory(Liste::class)->create(['user_id' => $user->id]);
        $list->meals()->attach([$item1->id, $item2->id]);
        $list->items()->attach([$item3->id, $item4->id, $item5->id, $item6->id, $item7->id, $item8->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('items.index', ['filter' => 'list:'.$list->id, 'sort' => 'meal']));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $item7->id,
                        'name' => $item7->name,
                    ],
                    [
                        'id' => $item8->id,
                        'name' => $item8->name,
                    ],
                    [
                        'id' => $item5->id,
                        'name' => $item5->name,
                    ],
                    [
                        'id' => $item6->id,
                        'name' => $item6->name,
                    ],
                    [
                        'id' => $item3->id,
                        'name' => $item3->name,
                    ],
                    [
                        'id' => $item4->id,
                        'name' => $item4->name,
                    ],
                ],
            ])
            ->assertJsonMissing([
                'id' => $item1->id,
                'name' => $item1->name,
            ])
            ->assertJsonMissing([
                'id' => $item2->id,
                'name' => $item2->name,
            ]);
    }

    /** @test */
    public function getting_all_shopping_list_items_ordered_by_meal_name_desc()
    {
        $user = factory(User::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $item3 = factory(Item::class)->create(['user_id' => $user->id]);
        $item4 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal1 = factory(Meal::class)->create(['user_id' => $user->id, 'name' => 'Last Meal']);
        $item5 = factory(Item::class)->create(['user_id' => $user->id]);
        $item6 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal1->items()->attach([$item5->id, $item6->id]);
        $meal2 = factory(Meal::class)->create(['user_id' => $user->id, 'name' => 'First Meal']);
        $item7 = factory(Item::class)->create(['user_id' => $user->id]);
        $item8 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal2->items()->attach([$item7->id, $item8->id]);
        $list = factory(Liste::class)->create(['user_id' => $user->id]);
        $list->meals()->attach([$item1->id, $item2->id]);
        $list->items()->attach([$item3->id, $item4->id, $item5->id, $item6->id, $item7->id, $item8->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('items.index', ['filter' => 'list:'.$list->id, 'sort' => '-meal']));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $item5->id,
                        'name' => $item5->name,
                    ],
                    [
                        'id' => $item6->id,
                        'name' => $item6->name,
                    ],
                    [
                        'id' => $item7->id,
                        'name' => $item7->name,
                    ],
                    [
                        'id' => $item8->id,
                        'name' => $item8->name,
                    ],
                    [
                        'id' => $item3->id,
                        'name' => $item3->name,
                    ],
                    [
                        'id' => $item4->id,
                        'name' => $item4->name,
                    ],
                ],
            ])
            ->assertJsonMissing([
                'id' => $item1->id,
                'name' => $item1->name,
            ])
            ->assertJsonMissing([
                'id' => $item2->id,
                'name' => $item2->name,
            ]);
    }

    /** @test */
    public function getting_all_shopping_list_items_for_a_list_that_isnt_the_logged_in_users_returns_a_403()
    {
        $list1 = factory(Liste::class)->states(['with_items'])->create();
        $list2 = factory(Liste::class)->states(['with_items'])->create();

        Sanctum::actingAs($list1->user, ['*']);

        $response = $this->getJson(route('items.index', ['filter' => 'list:'.$list2->id]));

        $response->assertStatus(403);
    }

    /** @test */
    public function getting_all_meal_items()
    {
        $meal1 = factory(Meal::class)->states(['with_items'])->create();
        $meal2 = factory(Meal::class)->states(['with_items'])->create();

        Sanctum::actingAs($meal1->user, ['*']);

        $response = $this->getJson(route('items.index', ['filter' => 'meal:'.$meal1->id]));

        $response->assertOk();

        $meal1->items->each(function ($item) use ($response) {
            $response->assertJsonFragment(['name' => $item->name]);
        });

        $meal2->items->each(function ($item) use ($response) {
            $response->assertJsonMissing(['name' => $item->name]);
        });
    }

    /** @test */
    public function getting_all_meal_items_for_a_meal_that_isnt_the_logged_in_users_returns_a_403()
    {
        $meal1 = factory(Meal::class)->states(['with_items'])->create();
        $meal2 = factory(Meal::class)->states(['with_items'])->create();

        Sanctum::actingAs($meal1->user, ['*']);

        $response = $this->getJson(route('items.index', ['filter' => 'meal:'.$meal2->id]));

        $response->assertStatus(403);
    }

    /** @test */
    public function creating_a_new_item()
    {
        Sanctum::actingAs(
            $user = factory(User::class)->create(),
            ['*'],
        );

        $response = $this->postJson(route('items.store'), ['name' => 'Test Item']);

        $response
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'user_id' => $user->id,
                    'name' => 'Test Item',
                ],
            ]);

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'Test Item',
        ]);
    }

    /** @test */
    public function creating_a_new_shopping_list_item()
    {
        $user = factory(User::class)->create();
        $list = factory(Liste::class)->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson(route('items.store'), [
            'name' => 'Test Shopping List Item',
            'list_id' => $list->id,
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'user_id' => $user->id,
                    'name' => 'Test Shopping List Item',
                ],
            ]);

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'Test Shopping List Item',
        ]);
        $this->assertEquals(1, $list->items->count());
    }

    /** @test */
    public function creating_a_new_shopping_list_item_for_a_list_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $list1 = factory(Liste::class)->create(['user_id' => $user1->id]);
        $user2 = factory(User::class)->create();
        $list2 = factory(Liste::class)->create(['user_id' => $user2->id]);

        Sanctum::actingAs($user1, ['*']);

        $response = $this->postJson(route('items.store'), [
            'name' => 'Test Shopping List Item',
            'list_id' => $list2->id,
        ]);

        $response->assertStatus(403);

        $this->assertEquals(0, Item::count());
    }

    /** @test */
    public function creating_a_new_meal_item()
    {
        $user = factory(User::class)->create();
        $meal = factory(Meal::class)->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson(route('items.store'), [
            'name' => 'Test Meal Item',
            'meal_id' => $meal->id,
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'data' => ['name' => 'Test Meal Item'],
            ]);

        $this->assertDatabaseHas('items', ['name' => 'Test Meal Item']);
        $this->assertEquals(1, $meal->items->count());
    }

    /** @test */
    public function creating_a_new_meal_item_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $meal1 = factory(Meal::class)->create(['user_id' => $user1->id]);
        $user2 = factory(User::class)->create();
        $meal2 = factory(Meal::class)->create(['user_id' => $user2->id]);

        Sanctum::actingAs($user1, ['*']);

        $response = $this->postJson(route('items.store'), [
            'name' => 'Test Meal Item',
            'meal_id' => $meal2->id,
        ]);

        $response->assertStatus(403);

        $this->assertEquals(0, Item::count());
    }

    /** @test */
    public function adding_an_existing_item_to_a_shopping_list()
    {
        $item = factory(Item::class)->create();
        $user = factory(User::class)->create();
        $list = factory(Liste::class)->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson(route('items.store'), [
            'item_id' => $item->id,
            'list_id' => $list->id,
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $item->id,
                    'name' => $item->name,
                ],
            ]);

        $this->assertDatabaseHas('items', ['name' => $item->name]);
        $this->assertEquals(1, $list->items->count());
    }

    /** @test */
    public function adding_an_existing_item_to_a_shopping_list_that_isnt_the_logged_in_users_returns_a_403()
    {
        $item = factory(Item::class)->create();
        $user1 = factory(User::class)->create();
        $list1 = factory(Liste::class)->create(['user_id' => $user1->id]);
        $user2 = factory(User::class)->create();
        $list2 = factory(Liste::class)->create(['user_id' => $user2->id]);

        Sanctum::actingAs($user1, ['*']);

        $response = $this->postJson(route('items.store'), [
            'item_id' => $item->id,
            'list_id' => $list2->id,
        ]);

        $response->assertStatus(403);

        $this->assertEquals(0, $list2->items->count());
    }

    /** @test */
    public function adding_an_existing_item_to_a_meal()
    {
        $item = factory(Item::class)->create();
        $user = factory(User::class)->create();
        $meal = factory(Meal::class)->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson(route('items.store'), [
            'item_id' => $item->id,
            'meal_id' => $meal->id,
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $item->id,
                    'name' => $item->name,
                ],
            ]);

        $this->assertDatabaseHas('items', ['name' => $item->name]);
        $this->assertEquals(1, $meal->items->count());
    }

    /** @test */
    public function adding_an_existing_item_to_a_meal_that_isnt_the_logged_in_users_returns_a_403()
    {
        $item = factory(Item::class)->create();
        $user1 = factory(User::class)->create();
        $meal1 = factory(Meal::class)->create(['user_id' => $user1->id]);
        $user2 = factory(User::class)->create();
        $meal2 = factory(Meal::class)->create(['user_id' => $user2->id]);

        Sanctum::actingAs($user1, ['*']);

        $response = $this->postJson(route('items.store'), [
            'item_id' => $item->id,
            'meal_id' => $meal2->id,
        ]);

        $response->assertStatus(403);

        $this->assertEquals(0, $meal2->items->count());
    }

    /** @test */
    public function updating_an_item()
    {
        $user = factory(User::class)->create();
        $item = factory(Item::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Item',
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->patchJson(route('items.update', $item), ['name' => 'Updated Item']);

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $item->id,
                    'name' => 'Updated Item',
                ],
            ]);

        $this->assertDatabaseMissing('items', ['name' => 'Test Item']);
        $this->assertDatabaseHas('items', ['name' => 'Updated Item']);
    }

    /** @test */
    public function updating_an_item_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $item = factory(Item::class)->create([
            'user_id' => $user1->id,
            'name' => 'Test Item',
        ]);

        Sanctum::actingAs($user2, ['*']);

        $response = $this->patchJson(route('items.update', $item), ['name' => 'Updated Item']);

        $response->assertStatus(403);

        $this->assertDatabaseHas('items', ['name' => 'Test Item']);
        $this->assertDatabaseMissing('items', ['name' => 'Updated Item']);
    }

    /** @test */
    public function updating_a_shopping_list_item()
    {
        $user = factory(User::class)->create();
        $item = factory(Item::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Shopping List Item',
        ]);
        $list = factory(Liste::class)->create(['user_id' => $user->id]);
        $list->items()->attach($item);

        Sanctum::actingAs($user, ['*']);

        $response = $this->patchJson(route('items.update', $item), [
            'name' => 'Updated Shopping List Item',
            'list_id' => $list->id,
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $item->id,
                    'name' => 'Updated Shopping List Item',
                ],
            ]);

        $this->assertDatabaseMissing('items', ['name' => 'Test Shopping List Item']);
        $this->assertDatabaseHas('items', ['name' => 'Updated Shopping List Item']);
        $this->assertEquals(1, $list->items->count());
    }

    /** @test */
    public function updating_a_meal_item()
    {
        $user = factory(User::class)->create();
        $item = factory(Item::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Meal Item',
        ]);
        $meal = factory(Meal::class)->create(['user_id' => $user->id]);
        $meal->items()->attach($item);

        Sanctum::actingAs($user, ['*']);

        $response = $this->patchJson(route('items.update', $item), [
            'name' => 'Updated Meal Item',
            'meal_id' => $meal->id,
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $item->id,
                    'name' => 'Updated Meal Item',
                ],
            ]);

        $this->assertDatabaseMissing('items', ['name' => 'Test Meal Item']);
        $this->assertDatabaseHas('items', ['name' => 'Updated Meal Item']);
        $this->assertEquals(1, $meal->items->count());
    }

    /** @test */
    public function deleting_an_item()
    {
        $user = factory(User::class)->create();
        $item = factory(Item::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Item',
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->deleteJson(route('items.destroy', $item));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('items', ['name' => 'Test Item']);
        $this->assertEquals(0, Item::count());
    }

    /** @test */
    public function deleting_an_item_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $item = factory(Item::class)->create([
            'user_id' => $user1->id,
            'name' => 'Test Item',
        ]);

        Sanctum::actingAs($user2, ['*']);

        $response = $this->deleteJson(route('items.destroy', $item));

        $response->assertStatus(403);

        $this->assertDatabaseHas('items', ['name' => 'Test Item']);
        $this->assertEquals(1, Item::count());
    }

    /** @test */
    public function deleting_a_shopping_list_item()
    {
        $user = factory(User::class)->create();
        $item = factory(Item::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Shopping List Item',
        ]);
        $list = factory(Liste::class)->create(['user_id' => $user->id]);
        $list->items()->attach($item);

        Sanctum::actingAs($user, ['*']);

        $response = $this->deleteJson(route('items.destroy', $item), ['list_id' => $list->id]);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(0, Item::count());
        $this->assertEquals(0, $list->items->count());
    }

    /** @test */
    public function deleting_a_meal_item()
    {
        $user = factory(User::class)->create();
        $item = factory(Item::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Meal Item',
        ]);
        $meal = factory(Meal::class)->create(['user_id' => $user->id]);
        $meal->items()->attach($item);

        Sanctum::actingAs($user, ['*']);

        $response = $this->deleteJson(route('items.destroy', $item), ['meal_id' => $meal->id]);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('items', ['name' => 'Test Meal Item']);
        $this->assertEquals(0, Item::count());
        $this->assertEquals(0, $meal->items->count());
    }

    /**
     * @test
     * @dataProvider itemIdInputValidation
     * @dataProvider itemNameInputValidation
     */
    public function test_store_form_validation($formInput, $formInputValue)
    {
        Sanctum::actingAs(factory(User::class)->create(), ['*']);

        $response = $this->postJson(route('items.store'), [$formInput => $formInputValue]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    /**
     * @test
     * @dataProvider itemNameInputValidation
     */
    public function test_update_form_validation($formInput, $formInputValue)
    {
        $user = factory(User::class)->create();
        $item = factory(Item::class)->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->patchJson(route('items.update', $item), [$formInput => $formInputValue]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    public function itemIdInputValidation()
    {
        return [
            'Item ID is required when no name is provided' => ['item_id', null],
            'Item ID is an integer' => ['item_id', 'String'],
            'Item ID exists in items table' => ['item_id', 1],
        ];
    }

    public function itemNameInputValidation()
    {
        return [
            'Item Name is required when no item ID is provided' => ['name', ''],
            'Item Name is no longer than 250 characters' => ['name', Str::random(251)],
        ];
    }
}
