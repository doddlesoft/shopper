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

class MealControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function getting_all_meals()
    {
        $meal1 = factory(Meal::class)->create();
        $meal2 = factory(Meal::class)->create();

        Sanctum::actingAs($meal1->user, ['*']);

        $response = $this->getJson(route('meals.index'));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $meal1->id,
                        'name' => $meal1->name,
                    ],
                ],
            ])
            ->assertJsonMissing([
                'id' => $meal2->id,
                'name' => $meal2->name,
            ]);
    }

    /** @test */
    public function getting_a_meal()
    {
        $meal = factory(Meal::class)->create(['name' => 'Test Meal']);

        Sanctum::actingAs($meal->user, ['*']);

        $response = $this->getJson(route('meals.show', $meal));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $meal->id,
                    'name' => $meal->name,
                ],
            ]);
    }

    /** @test */
    public function getting_a_meal_that_isnt_the_logged_in_users_returns_a_403()
    {
        $meal1 = factory(Meal::class)->create();
        $meal2 = factory(Meal::class)->create();

        Sanctum::actingAs($meal1->user, ['*']);

        $response = $this->getJson(route('meals.show', $meal2));

        $response->assertStatus(403);
    }

    /** @test */
    public function getting_a_meal_including_its_items_and_lists()
    {
        $user = factory(User::class)->create();
        $item1 = factory(Item::class)->create(['user_id' => $user->id]);
        $item2 = factory(Item::class)->create(['user_id' => $user->id]);
        $meal = factory(Meal::class)->create(['user_id' => $user->id]);
        $meal->items()->attach([$item1->id, $item2->id]);
        $list1 = factory(Liste::class)->create(['user_id' => $user->id]);
        $list1->meals()->attach($meal);
        $list2 = factory(Liste::class)->create(['user_id' => $user->id]);
        $list2->meals()->attach($meal);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('meals.show', ['meal' => $meal, 'include' => 'items,lists']));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $meal->id,
                    'name' => $meal->name,
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
                    'lists' => [
                        [
                            'id' => $list1->id,
                            'name' => $list1->name,
                        ],
                        [
                            'id' => $list2->id,
                            'name' => $list2->name,
                        ],
                    ],
                ],
            ]);
    }

    /** @test */
    public function creating_a_meal()
    {
        Sanctum::actingAs(
            $user = factory(User::class)->create(),
            ['*'],
        );

        $response = $this->postJson(route('meals.store'), ['name' => 'Test Meal']);

        $response
            ->assertCreated()
            ->assertJson([
                'data' => [
                    'user_id' => $user->id,
                    'name' => 'Test Meal',
                ],
            ]);

        $this->assertDatabaseHas('meals', [
            'user_id' => $user->id,
            'name' => 'Test Meal',
        ]);
    }

    /** @test */
    public function updating_a_meal()
    {
        $user = factory(User::class)->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Meal',
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->patchJson(route('meals.update', $meal), ['name' => 'Updated Meal']);

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $meal->id,
                    'name' => 'Updated Meal',
                ],
            ]);

        $this->assertDatabaseMissing('meals', ['name' => 'Test Meal']);
        $this->assertDatabaseHas('meals', ['name' => 'Updated Meal']);
    }

    /** @test */
    public function updating_a_meal_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user1->id,
            'name' => 'Test Meal',
        ]);

        Sanctum::actingAs($user2, ['*']);

        $response = $this->patchJson(route('meals.update', $meal), ['name' => 'Updated Meal']);

        $response->assertStatus(403);

        $this->assertDatabaseHas('meals', ['name' => 'Test Meal']);
        $this->assertDatabaseMissing('meals', ['name' => 'Updated Meal']);
    }

    /** @test */
    public function deleting_a_meal()
    {
        $user = factory(User::class)->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Meal',
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->deleteJson(route('meals.destroy', $meal));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('meals', ['name' => 'Test Meal']);
        $this->assertEquals(0, Meal::count());
    }

    /** @test */
    public function deleting_a_meal_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $meal = factory(Meal::class)->create([
            'user_id' => $user1->id,
            'name' => 'Test Meal',
        ]);

        Sanctum::actingAs($user2, ['*']);

        $response = $this->deleteJson(route('meals.destroy', $meal));

        $response->assertStatus(403);

        $this->assertDatabaseHas('meals', ['name' => 'Test Meal']);
        $this->assertEquals(1, Meal::count());
    }

    /**
     * @test
     * @dataProvider nameInputValidation
     */
    public function test_store_form_validation($formInput, $formInputValue)
    {
        Sanctum::actingAs(factory(User::class)->create(), ['*']);

        $response = $this->postJson(route('meals.store'), [$formInput => $formInputValue]);

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
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
            'name' => 'Test Meal',
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->patchJson(route('meals.update', $meal), [$formInput => $formInputValue]);

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
}
