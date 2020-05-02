<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
