<?php

namespace Tests\Feature\Http\Controller\Api;

use App\Item;
use App\Liste;
use App\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function getting_all_items()
    {
        $item = factory(Item::class)->create(['name' => 'Test Item']);

        $response = $this->getJson(route('items.index'));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $item->id,
                        'name' => $item->name,
                    ],
                ],
            ]);
    }

    /** @test */
    public function getting_paginated_items()
    {
        $item1 = factory(Item::class)->create(['name' => 'First Item']);
        $item2 = factory(Item::class)->create(['name' => 'Second Item']);
        $item3 = factory(Item::class)->create(['name' => 'Third Item']);
        $item4 = factory(Item::class)->create(['name' => 'Fourth Item']);
        $item5 = factory(Item::class)->create(['name' => 'Fifth Item']);

        $response = $this->getJson(route('items.index', ['page' => 2, 'per_page' => 2]));

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
        $item1 = factory(Item::class)->create(['created_at' => now()]);
        $item2 = factory(Item::class)->create(['created_at' => now()->subDay()]);

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
        $item1 = factory(Item::class)->create(['created_at' => now()->subDay()]);
        $item2 = factory(Item::class)->create(['created_at' => now()]);

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
        $item1 = factory(Item::class)->create(['name' => 'Last Item']);
        $item2 = factory(Item::class)->create(['name' => 'First Item']);

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
        $item1 = factory(Item::class)->create(['name' => 'First Item']);
        $item2 = factory(Item::class)->create(['name' => 'Last Item']);

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
        $item1 = factory(Item::class)->create(['name' => 'Fifth Item']);
        $item2 = factory(Item::class)->create(['name' => 'Sixth Item']);
        $meal1 = factory(Meal::class)->create(['name' => 'Last Meal']);
        $item3 = factory(Item::class)->create(['name' => 'Third Item']);
        $item4 = factory(Item::class)->create(['name' => 'Fourth Item']);
        $meal1->items()->attach([$item3->id, $item4->id]);
        $meal2 = factory(Meal::class)->create(['name' => 'First Meal']);
        $item5 = factory(Item::class)->create(['name' => 'First Item']);
        $item6 = factory(Item::class)->create(['name' => 'Second Item']);
        $meal2->items()->attach([$item5->id, $item6->id]);

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
        $item1 = factory(Item::class)->create(['name' => 'Fifth Item']);
        $item2 = factory(Item::class)->create(['name' => 'Sixth Item']);
        $meal1 = factory(Meal::class)->create(['name' => 'Last Meal']);
        $item3 = factory(Item::class)->create(['name' => 'Third Item']);
        $item4 = factory(Item::class)->create(['name' => 'Fourth Item']);
        $meal1->items()->attach([$item3->id, $item4->id]);
        $meal2 = factory(Meal::class)->create(['name' => 'First Meal']);
        $item5 = factory(Item::class)->create(['name' => 'First Item']);
        $item6 = factory(Item::class)->create(['name' => 'Second Item']);
        $meal2->items()->attach([$item5->id, $item6->id]);

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
        $item1 = factory(Item::class)->create(['name' => 'Missing First Item']);
        $item2 = factory(Item::class)->create(['name' => 'Missing Second Item']);
        $item3 = factory(Item::class)->create(['name' => 'Fifth Item']);
        $item4 = factory(Item::class)->create(['name' => 'Sixth Item']);
        $meal1 = factory(Meal::class)->create(['name' => 'Last Meal']);
        $item5 = factory(Item::class)->create(['name' => 'Third Item']);
        $item6 = factory(Item::class)->create(['name' => 'Fourth Item']);
        $meal1->items()->attach([$item5->id, $item6->id]);
        $meal2 = factory(Meal::class)->create(['name' => 'First Meal']);
        $item7 = factory(Item::class)->create(['name' => 'First Item']);
        $item8 = factory(Item::class)->create(['name' => 'Second Item']);
        $meal2->items()->attach([$item7->id, $item8->id]);
        $list = factory(Liste::class)->create();
        $list->meals()->attach([$item1->id, $item2->id]);
        $list->items()->attach([$item3->id, $item4->id, $item5->id, $item6->id, $item7->id, $item8->id]);

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
        $item1 = factory(Item::class)->create(['name' => 'Missing First Item']);
        $item2 = factory(Item::class)->create(['name' => 'Missing Second Item']);
        $item3 = factory(Item::class)->create(['name' => 'Fifth Item']);
        $item4 = factory(Item::class)->create(['name' => 'Sixth Item']);
        $meal1 = factory(Meal::class)->create(['name' => 'Last Meal']);
        $item5 = factory(Item::class)->create(['name' => 'Third Item']);
        $item6 = factory(Item::class)->create(['name' => 'Fourth Item']);
        $meal1->items()->attach([$item5->id, $item6->id]);
        $meal2 = factory(Meal::class)->create(['name' => 'First Meal']);
        $item7 = factory(Item::class)->create(['name' => 'First Item']);
        $item8 = factory(Item::class)->create(['name' => 'Second Item']);
        $meal2->items()->attach([$item7->id, $item8->id]);
        $list = factory(Liste::class)->create();
        $list->meals()->attach([$item1->id, $item2->id]);
        $list->items()->attach([$item3->id, $item4->id, $item5->id, $item6->id, $item7->id, $item8->id]);

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
    public function getting_all_meal_items()
    {
        $meal1 = factory(Meal::class)->states(['with_items'])->create();
        $meal2 = factory(Meal::class)->states(['with_items'])->create();

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
    public function creating_a_new_item()
    {
        $response = $this->postJson(route('items.store'), ['name' => 'Test Item']);

        $response
            ->assertCreated()
            ->assertJson([
                'data' => ['name' => 'Test Item'],
            ]);

        $this->assertDatabaseHas('items', ['name' => 'Test Item']);
    }

    /** @test */
    public function creating_a_new_shopping_list_item()
    {
        $list = factory(Liste::class)->create();

        $response = $this->postJson(route('items.store'), [
            'name' => 'Test Shopping List Item',
            'list_id' => $list->id,
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'data' => ['name' => 'Test Shopping List Item'],
            ]);

        $this->assertDatabaseHas('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(1, $list->items->count());
    }

    /** @test */
    public function creating_a_new_meal_item()
    {
        $meal = factory(Meal::class)->create();

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
    public function adding_an_existing_item_to_a_shopping_list()
    {
        $item = factory(Item::class)->create();
        $list = factory(Liste::class)->create();

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
    public function adding_an_existing_item_to_a_meal()
    {
        $item = factory(Item::class)->create();
        $meal = factory(Meal::class)->create();

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
    public function updating_an_item()
    {
        $item = factory(Item::class)->create(['name' => 'Test Item']);

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
    public function updating_a_shopping_list_item()
    {
        $item = factory(Item::class)->create(['name' => 'Test Shopping List Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);

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
        $item = factory(Item::class)->create(['name' => 'Test Meal Item']);
        $meal = factory(Meal::class)->create();
        $meal->items()->attach($item);

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
        $item = factory(Item::class)->create(['name' => 'Test Item']);

        $response = $this->deleteJson(route('items.destroy', $item));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('items', ['name' => 'Test Item']);
        $this->assertEquals(0, Item::count());
    }

    /** @test */
    public function deleting_a_shopping_list_item()
    {
        $item = factory(Item::class)->create(['name' => 'Test Shopping List Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);

        $response = $this->deleteJson(route('items.destroy', $item), ['list_id' => $list->id]);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(0, Item::count());
        $this->assertEquals(0, $list->items->count());
    }

    /** @test */
    public function deleting_a_meal_item()
    {
        $item = factory(Item::class)->create(['name' => 'Test Meal Item']);
        $meal = factory(Meal::class)->create();
        $meal->items()->attach($item);

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
        $item = factory(Item::class)->create();

        $response = $this->patchJson(route('items.update', $item), [$formInput => $formInputValue]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    public function itemIdInputValidation()
    {
        return [
            'Item ID is required when no name is provided' => ['item_id', null],
            'Item ID must be an integer' => ['item_id', 'String'],
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
