<?php

namespace Tests\Feature\Http\Controller\Api;

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
            ->assertJsonFragment(['name' => $list1->name])
            ->assertJsonFragment(['name' => $list2->name]);
    }

    /** @test */
    public function getting_a_shopping_list()
    {
        $list = factory(Liste::class)->create(['name' => 'Test Shopping List']);

        $response = $this->getJson(route('lists.show', $list));

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Test Shopping List',
            ]);
    }

    /** @test */
    public function creating_a_shopping_list()
    {
        $response = $this->postJson(route('lists.store'), ['name' => 'Test Shopping List']);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'name' => 'Test Shopping List',
            ]);

        $this->assertDatabaseHas('lists', ['name' => 'Test Shopping List']);
    }

    /** @test */
    public function updating_a_shopping_list()
    {
        $list = factory(Liste::class)->create(['name' => 'Test Shopping List']);

        $response = $this->patchJson(route('lists.update', $list), ['name' => 'Updated Shopping List']);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Updated Shopping List',
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
}
