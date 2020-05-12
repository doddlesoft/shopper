<?php

namespace Tests\Feature\Http\Controller\Api;

use App\Liste;
use App\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListMealControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function getting_all_list_meals()
    {
        $list = factory(Liste::class)->create();
        $meal = factory(Meal::class)->create();
        $list->meals()->attach($meal);

        $response = $this->getJson(route('lists.meals.index', $list));

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => $meal->name]);
    }

    /** @test */
    public function creating_a_list_meal()
    {
        $list = factory(Liste::class)->create(['name' => 'Shopping List']);
        $meal = factory(Meal::class)->create(['name' => 'Meal']);

        $response = $this->postJson(route('lists.meals.store', $list), ['meal_id' => $meal->id]);

        $response->assertStatus(204);

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

        $response = $this->postJson(route('lists.meals.store', $list), [$formInput => $formInputValue]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    public function mealIdInputValidation()
    {
        return [
            'Meal ID is required' => ['meal_id', null],
            'Meal ID must be an integer' => ['meal_id', 'String'],
            'Meal ID exists in the meal table' => ['meal_id', 1],
        ];
    }
}
