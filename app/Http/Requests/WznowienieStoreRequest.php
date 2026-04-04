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
            'text' => ['required', 'string'],
            'id_zapytania' => ['required', 'exists:zapytanias,id'],
            'id_user' => ['required', 'exists:users,id'],
        ];
    }
}
