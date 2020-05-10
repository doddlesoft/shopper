<?php

namespace Tests\Feature\Http\Controller\Api;

use App\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MealControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function getting_all_meals()
    {
        $meal1 = factory(Meal::class)->create();
        $meal2 = factory(Meal::class)->create();

        $response = $this->getJson(route('meals.index'));

        $response
            ->assertOk()
            ->assertJsonFragment(['name' => $meal1->name])
            ->assertJsonFragment(['name' => $meal2->name]);
    }

    /** @test */
    public function getting_a_meal()
    {
        $meal = factory(Meal::class)->create(['name' => 'Test Meal']);

        $response = $this->getJson(route('meals.show', $meal));

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Test Meal',
            ]);
    }

    /** @test */
    public function creating_a_meal()
    {
        $response = $this->postJson(route('meals.store'), ['name' => 'Test Meal']);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'name' => 'Test Meal',
            ]);

        $this->assertDatabaseHas('meals', ['name' => 'Test Meal']);
    }

    /** @test */
    public function updating_a_meal()
    {
        $meal = factory(Meal::class)->create(['name' => 'Test Meal']);

        $response = $this->patchJson(route('meals.update', $meal), ['name' => 'Updated Meal']);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Updated Meal',
            ]);

        $this->assertDatabaseMissing('meals', ['name' => 'Test Meal']);
        $this->assertDatabaseHas('meals', ['name' => 'Updated Meal']);
    }

    /** @test */
    public function deleting_a_meal()
    {
        $meal = factory(Meal::class)->create(['name' => 'Test Meal']);

        $response = $this->deleteJson(route('meals.destroy', $meal));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('meals', ['name' => 'Test Meal']);
        $this->assertEquals(0, Meal::count());
    }

    /**
     * @test
     * @dataProvider nameInputValidation
     */
    public function test_store_form_validation($formInput, $formInputValue)
    {
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
        $meal = factory(Meal::class)->create(['name' => 'Test Meal']);

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
