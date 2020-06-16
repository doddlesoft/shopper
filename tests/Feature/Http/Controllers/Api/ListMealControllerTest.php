<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Liste;
use App\Meal;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListMealControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function getting_all_list_meals()
    {
        $user = factory(User::class)->create();
        $list = factory(Liste::class)->create(['user_id' => $user->id]);
        $meal = factory(Meal::class)->create(['user_id' => $user->id]);
        $list->meals()->attach($meal);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('list-meals.index', $list));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $meal->id,
                        'name' => $meal->name,
                    ],
                ],
            ]);
    }

    /** @test */
    public function getting_all_list_meals_for_a_list_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $list = factory(Liste::class)->create(['user_id' => $user1->id]);
        $meal = factory(Meal::class)->create(['user_id' => $user1->id]);
        $list->meals()->attach($meal);

        Sanctum::actingAs($user2, ['*']);

        $response = $this->getJson(route('list-meals.index', $list));

        $response->assertStatus(403);
    }

    /** @test */
    public function creating_a_list_meal()
    {
        $user = factory(User::class)->create();
        $list = factory(Liste::class)->create(['user_id' => $user->id, 'name' => 'Shopping List']);
        $meal = factory(Meal::class)->create(['user_id' => $user->id, 'name' => 'Meal']);

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson(route('list-meals.store', $list), ['meal_id' => $meal->id]);

        $response->assertStatus(204);

        $this->assertDatabaseHas('list_meal', [
            'list_id' => $list->id,
            'meal_id' => $meal->id,
        ]);
    }

    /** @test */
    public function creating_a_list_meal_for_a_list_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $list = factory(Liste::class)->create(['user_id' => $user1->id, 'name' => 'Shopping List']);
        $meal = factory(Meal::class)->create(['user_id' => $user1->id, 'name' => 'Meal']);

        Sanctum::actingAs($user2, ['*']);

        $response = $this->postJson(route('list-meals.store', $list), ['meal_id' => $meal->id]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('list_meal', [
            'list_id' => $list->id,
            'meal_id' => $meal->id,
        ]);
    }

    /** @test */
    public function creating_a_list_meal_for_a_meal_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $list = factory(Liste::class)->create(['user_id' => $user1->id, 'name' => 'Shopping List']);
        $meal = factory(Meal::class)->create(['user_id' => $user2->id, 'name' => 'Meal']);

        Sanctum::actingAs($user1, ['*']);

        $response = $this->postJson(route('list-meals.store', $list), ['meal_id' => $meal->id]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('list_meal', [
            'list_id' => $list->id,
            'meal_id' => $meal->id,
        ]);
    }

    /** @test */
    public function deleting_a_list_meal()
    {
        $user = factory(User::class)->create();
        $list = factory(Liste::class)->create(['user_id' => $user->id, 'name' => 'Shopping List']);
        $meal = factory(Meal::class)->create(['user_id' => $user->id, 'name' => 'Meal']);
        $list->meals()->attach($meal);

        Sanctum::actingAs($user, ['*']);

        $response = $this->deleteJson(route('list-meals.destroy', $list), ['meal_id' => $meal->id]);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('list_meal', [
            'list_id' => $list->id,
            'meal_id' => $meal->id,
        ]);
    }

    /** @test */
    public function deleting_a_list_meal_from_a_list_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $list = factory(Liste::class)->create(['user_id' => $user1->id, 'name' => 'Shopping List']);
        $meal = factory(Meal::class)->create(['user_id' => $user1->id, 'name' => 'Meal']);
        $list->meals()->attach($meal);

        Sanctum::actingAs($user2, ['*']);

        $response = $this->deleteJson(route('list-meals.destroy', $list), ['meal_id' => $meal->id]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('list_meal', [
            'list_id' => $list->id,
            'meal_id' => $meal->id,
        ]);
    }

    /** @test */
    public function deleting_a_list_meal_for_a_meal_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $list = factory(Liste::class)->create(['user_id' => $user1->id, 'name' => 'Shopping List']);
        $meal = factory(Meal::class)->create(['user_id' => $user2->id, 'name' => 'Meal']);
        $list->meals()->attach($meal);

        Sanctum::actingAs($user1, ['*']);

        $response = $this->deleteJson(route('list-meals.destroy', $list), ['meal_id' => $meal->id]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('list_meal', [
            'list_id' => $list->id,
            'meal_id' => $meal->id,
        ]);
    }

    /**
     * @test
     * @dataProvider mealIdInputValidation
     */
    public function test_store_form_validation($formInput, $formInputValue)
    {
        $list = factory(Liste::class)->create();

        Sanctum::actingAs($list->user, ['*']);

        $response = $this->postJson(route('list-meals.store', $list), [$formInput => $formInputValue]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    /**
     * @test
     * @dataProvider mealIdInputValidation
     */
    public function test_delete_form_validation($formInput, $formInputValue)
    {
        $list = factory(Liste::class)->create();

        Sanctum::actingAs($list->user, ['*']);

        $response = $this->postJson(route('list-meals.store', $list), [$formInput => $formInputValue]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    public function mealIdInputValidation()
    {
        return [
            'Meal ID is required' => ['meal_id', null],
            'Meal ID is an integer' => ['meal_id', 'String'],
            'Meal ID exists in the meals table' => ['meal_id', 1],
        ];
    }
}
