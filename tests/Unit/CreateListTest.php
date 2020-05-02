<?php

namespace Tests\Unit;

use App\Lists\Actions\CreateList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_shopping_list_is_created()
    {
        app(CreateList::class)->perform('Test Shopping List');

        $this->assertDatabaseHas('lists', ['name' => 'Test Shopping List']);
    }
}
