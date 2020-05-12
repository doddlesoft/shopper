<?php

namespace Tests\Feature\Http\Controller\Api;

use App\Item;
use App\Liste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompletedItemControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function completing_an_item()
    {
        $item = factory(Item::class)->create();
        $list = factory(Liste::class)->create();
        $list->items()->attach($item);

        $response = $this->postJson(route('completed-items.store'), [
            'item_id' => $item->id,
            'list_id' => $list->id,
        ]);

        $response->assertStatus(204);

        $this->assertNotNull($list->items->where('id', $item->id)->first()->pivot->completed_at);
    }

    /**
     * @test
     * @dataProvider itemIdInputValidation
     * @dataProvider listIdInputValidation
     */
    public function test_store_form_validation($formInput, $formInputValue)
    {
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
