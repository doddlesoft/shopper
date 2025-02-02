<?php

namespace Tests\Unit\Actions\Lists;

use App\Actions\Lists\UpdateList;
use App\Liste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_shopping_list_is_updated()
    {
        $list = factory(Liste::class)->create(['name' => 'Test Shopping List']);

        app(UpdateList::class)->perform($list, 'Updated Shopping List');

        $this->assertDatabaseMissing('lists', ['name' => 'Test Shopping List']);
        $this->assertDatabaseHas('lists', ['name' => 'Updated Shopping List']);
    }
}
