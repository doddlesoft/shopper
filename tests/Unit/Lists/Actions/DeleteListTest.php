<?php

namespace Tests\Unit;

use App\Liste;
use App\Lists\Actions\DeleteList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_shopping_list_is_deleted()
    {
        $list = factory(Liste::class)->create(['name' => 'Test Shopping List']);

        app(DeleteList::class)->perform($list);

        $this->assertDatabaseMissing('lists', ['name' => 'Test Shopping List']);
        $this->assertEquals(0, Liste::count());
    }
}
