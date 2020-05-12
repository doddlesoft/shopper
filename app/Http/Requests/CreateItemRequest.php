<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'item_id' => 'required_without:name|nullable|integer|exists:items,id',
            'name' => 'required_without:item_id|nullable|max:250',
        ];
    }
}
