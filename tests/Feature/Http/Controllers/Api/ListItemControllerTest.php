<?php

namespace Tests\Feature;

use App\Item;
use App\Liste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ListItemControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function getting_all_shopping_list_items()
    {
        $list1 = factory(Liste::class)->states('with_items')->create();
        $list2 = factory(Liste::class)->states('with_items')->create();

        $response = $this->getJson(route('lists.items.index', $list1));

        $response->assertOk();

        $list1->items->each(function ($item) use ($response) {
            $response->assertJsonFragment(['name' => $item->name]);
        });

        $list2->items->each(function ($item) use ($response) {
            $response->assertJsonMissing(['name' => $item->name]);
        });
    }

    /** @test */
    public function creating_a_new_shopping_list_item()
    {
        $list = factory(Liste::class)->create();

        $response = $this->postJson(route('lists.items.store', $list), ['name' => 'Test Shopping List Item']);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'name' => 'Test Shopping List Item',
            ]);

        $this->assertDatabaseHas('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(1, $list->items->count());
    }

    /** @test */
    public function adding_an_existing_item_to_a_shopping_list()
    {
        $item = factory(Item::class)->create();
        $list = factory(Liste::class)->create();

        $response = $this->postJson(route('lists.items.store', $list), ['item_id' => $item->id]);

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
    public function updating_a_shopping_list_item()
    {
        $item = factory(Item::class)->create(['name' => 'Test Shopping List Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);

        $response = $this->patchJson(route('lists.items.update', [$list, $item]), ['name' => 'Updated Shopping List Item']);

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
    public function deleting_a_shopping_list_item()
    {
        $item = factory(Item::class)->create(['name' => 'Test Shopping List Item']);
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);

        $response = $this->deleteJson(route('lists.items.destroy', [$list, $item]));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('items', ['name' => 'Test Shopping List Item']);
        $this->assertEquals(0, Item::count());
        $this->assertEquals(0, $list->items->count());
    }

    /**
     * @test
     * @dataProvider itemIdInputValidation
     * @dataProvider itemNameInputValidation
     */
    public function test_store_form_validation($formInput, $formInputValue)
    {
        $list = factory(Liste::class)->create();

        $response = $this->postJson(route('lists.items.store', $list), [$formInput => $formInputValue]);

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
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);

        $response = $this->patchJson(route('lists.items.update', [$list, $item]), [$formInput => $formInputValue]);

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
            'Name is required when no item ID is provided' => ['name', ''],
            'Name is no longer than 250 characters' => ['name', Str::random(251)],
        ];
    }
}
