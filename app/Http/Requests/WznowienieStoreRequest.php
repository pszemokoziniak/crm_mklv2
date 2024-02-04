<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WznowienieStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'zapytania_id' => ['required'],
            'description' => ['required'],
            'user_id' => ['required'],
        ];
    }
}
