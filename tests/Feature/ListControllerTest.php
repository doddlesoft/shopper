<?php

namespace Tests\Feature;

use App\Liste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ListControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_a_shopping_list()
    {
        $response = $this->postJson(route('lists.store'), ['name' => 'Test Shopping List']);

        $response
            ->assertCreated()
            ->assertJson([
                'name' => 'Test Shopping List',
            ]);

        $this->assertDatabaseHas('lists', ['name' => 'Test Shopping List']);
    }

    /** @test */
    public function updating_a_shopping_list()
    {
        $list = factory(Liste::class)->create(['name' => 'New Shopping List']);

        $response = $this->patchJson(route('lists.update', $list), ['name' => 'Updated Shopping List']);

        $response
            ->assertOk()
            ->assertJson([
                'name' => 'Updated Shopping List',
            ]);

        $this->assertDatabaseMissing('lists', ['name' => 'New Shopping List']);
        $this->assertDatabaseHas('lists', ['name' => 'Updated Shopping List']);
    }

    /**
     * @test
     * @dataProvider nameInputValidation
     */
    public function test_form_validation($formInput, $formInputValue)
    {
        $response = $this->postJson(route('lists.store', [$formInput => $formInputValue]));

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
