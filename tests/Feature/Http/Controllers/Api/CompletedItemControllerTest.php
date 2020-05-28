<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Item;
use App\Liste;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CompletedItemControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function completing_an_item()
    {
        $user = factory(User::class)->create();
        $item = factory(Item::class)->create(['user_id' => $user->id]);
        $list = factory(Liste::class)->create(['user_id' => $user->id]);
        $list->items()->attach($item);

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson(route('completed-items.store'), [
            'item_id' => $item->id,
            'list_id' => $list->id,
        ]);

        $response->assertStatus(204);

        $this->assertNotNull($list->items->where('id', $item->id)->first()->pivot->completed_at);
    }

    /** @test */
    public function completing_an_item_that_isnt_the_logged_in_users_returns_a_403()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $item = factory(Item::class)->create(['user_id' => $user1->id]);
        $list = factory(Liste::class)->create(['user_id' => $user1->id]);
        $list->items()->attach($item);

        Sanctum::actingAs($user2, ['*']);

        $response = $this->postJson(route('completed-items.store'), [
            'item_id' => $item->id,
            'list_id' => $list->id,
        ]);

        $response->assertStatus(403);

        $this->assertNull($list->items->where('id', $item->id)->first()->pivot->completed_at);
    }

    /**
     * @test
     * @dataProvider itemIdInputValidation
     * @dataProvider listIdInputValidation
     */
    public function test_store_form_validation($formInput, $formInputValue)
    {
        Sanctum::actingAs(factory(User::class)->create(), ['*']);

        $response = $this->postJson(route('completed-items.store'), [$formInput => $formInputValue]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($formInput);
    }

    public function itemIdInputValidation()
    {
        return [
            'Item ID is required' => ['item_id', null],
            'Item ID must be an integer' => ['item_id', 'String'],
            'Item ID exists in items table' => ['item_id', 1],
        ];
    }

    public function listIdInputValidation()
    {
        return [
            'List ID is required' => ['list_id', null],
            'List ID must be an integer' => ['list_id', 'String'],
            'List ID exists in lists table' => ['list_id', 1],
        ];
    }
}
