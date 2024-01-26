<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalutaStoreRequest extends FormRequest
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
            'id' => ['request'],
            'name' => ['required'],
            'user_id' => ['required']
        ];
    }
    public function messages() {
        return [
            'required'  => 'Pole jest wymagane.',
            'max' => 'Nazwa możne zawierać',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nazwa',
        ];
    }
}
