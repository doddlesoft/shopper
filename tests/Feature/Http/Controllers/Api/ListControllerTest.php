<?php

namespace Tests\Feature\Http\Controller\Api;

use App\Item;
use App\Itemable;
use App\Liste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ListControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function getting_all_shopping_lists()
    {
        $list1 = factory(Liste::class)->create();
        $list2 = factory(Liste::class)->create();

        $response = $this->getJson(route('lists.index'));

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => $list1->id,
                        'name' => $list1->name,
                    ],
                    [
                        'id' => $list2->id,
                        'name' => $list2->name,
                    ],
                ],
            ]);
    }

    /** @test */
    public function getting_a_shopping_list()
    {
        $list = factory(Liste::class)->create(['name' => 'Test Shopping List']);

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
    public function creating_a_shopping_list()
    {
        $response = $this->postJson(route('lists.store'), ['name' => 'Test Shopping List']);

        $response
            ->assertCreated()
            ->assertJson([
                'data' => ['name' => 'Test Shopping List'],
            ]);

        $this->assertDatabaseHas('lists', ['name' => 'Test Shopping List']);
    }

    /** @test */
    public function creating_a_shopping_list_from_another_shopping_list()
    {
        $list = factory(Liste::class)->create(['name' => 'First Shopping List']);
        $item1 = factory(Item::class)->create(['name' => 'First Item']);
        $item2 = factory(Item::class)->create(['name' => 'Second Item']);
        $list->items()->attach([$item1->id, $item2->id]);
        $this->assertEquals(2, Itemable::count());

        $response = $this->postJson(route('lists.store'), [
            'list_id' => $list->id,
            'name' => 'Second Shopping List',
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'data' => ['name' => 'Second Shopping List'],
            ]);

        $this->assertDatabaseHas('lists', ['name' => 'First Shopping List']);
        $this->assertEquals(2, $list->items->count());
        $this->assertDatabaseHas('lists', ['name' => 'Second Shopping List']);
        $this->assertEquals(4, Itemable::count());
    }

    /** @test */
    public function creating_a_shopping_list_from_another_shopping_list_with_only_incomplete_items()
    {
        $list = factory(Liste::class)->create(['name' => 'First Shopping List']);
        $item1 = factory(Item::class)->create(['name' => 'First Item']);
        $item2 = factory(Item::class)->create(['name' => 'Second Item']);
        $list->items()->attach($item1, ['completed_at' => now()]);
        $list->items()->attach($item2);
        $this->assertEquals(2, Itemable::count());

        $response = $this->postJson(route('lists.store'), [
            'list_id' => $list->id,
            'name' => 'Second Shopping List',
            'only_incomplete' => true,
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'data' => ['name' => 'Second Shopping List'],
            ]);

        $this->assertDatabaseHas('lists', ['name' => 'First Shopping List']);
        $this->assertEquals(2, $list->items->count());
        $this->assertDatabaseHas('lists', ['name' => 'Second Shopping List']);
        $this->assertEquals(3, Itemable::count());
    }

    /** @test */
    public function updating_a_shopping_list()
    {
        $list = factory(Liste::class)->create(['name' => 'Test Shopping List']);

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
    public function deleting_a_shopping_list()
    {
        $list = factory(Liste::class)->create(['name' => 'Test Shopping List']);

        $response = $this->deleteJson(route('lists.destroy', $list));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('lists', ['name' => 'Test Shopping List']);
        $this->assertEquals(0, Liste::count());
    }

    /**
     * @test
     * @dataProvider nameInputValidation
     * @dataProvider onlyIncompleteInputValidation
     */
    public function test_store_form_validation($formInput, $formInputValue)
    {
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
        $list = factory(Liste::class)->create(['name' => 'Test Shopping List']);

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
