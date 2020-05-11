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
            ->assertJsonFragment(['name' => 'Test Item']);
    }

    /** @test */
    public function getting_all_shopping_list_items()
    {
        $list1 = factory(Liste::class)->states('with_items')->create();
        $list2 = factory(Liste::class)->states('with_items')->create();

        $response = $this->getJson(route('items.index', ['list_id' => $list1->id]));

        $response->assertOk();

        $list1->items->each(function ($item) use ($response) {
            $response->assertJsonFragment(['name' => $item->name]);
        });

        $list2->items->each(function ($item) use ($response) {
            $response->assertJsonMissing(['name' => $item->name]);
        });
    }

    /** @test */
    public function getting_all_meal_items()
    {
        $meal1 = factory(Meal::class)->states('with_items')->create();
        $meal2 = factory(Meal::class)->states('with_items')->create();

        $response = $this->getJson(route('items.index', ['meal_id' => $meal1->id]));
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
            ->assertJsonFragment([
                'name' => 'Test Item',
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
            ->assertJsonFragment([
                'name' => 'Test Shopping List Item',
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
            ->assertJsonFragment([
                'name' => 'Test Meal Item',
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
            ->assertJsonFragment([
                'id' => $item->id,
                'name' => $item->name,
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
            ->assertJsonFragment([
                'id' => $item->id,
                'name' => $item->name,
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
            ->assertJsonFragment([
                'name' => 'Updated Item',
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
            ->assertJsonFragment([
                'name' => 'Updated Shopping List Item',
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
            ->assertJsonFragment([
                'name' => 'Updated Meal Item',
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
