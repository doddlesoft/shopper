<?php

namespace Tests\Unit\Lists\Actions;

use App\Liste;
use App\Lists\Actions\UpdateList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_shopping_list_is_updated()
    {
        $list = factory(Liste::class)->create(['name' => 'New Shopping List']);

        app(UpdateList::class)->perform($list, 'Updated Shopping List');

        $this->assertDatabaseMissing('lists', ['name' => 'New Shopping List']);
        $this->assertDatabaseHas('lists', ['name' => 'Updated Shopping List']);
    }
}
