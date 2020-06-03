<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncompleteItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'list_id' => 'required|integer|exists:lists,id',
        ];
    }
}
