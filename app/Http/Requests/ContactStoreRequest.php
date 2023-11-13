<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactStoreRequest extends FormRequest
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
            'nazwa' => ['required', 'max:50'],
            'ulica' => ['required', 'max:50'],
            'miasto' => ['required', 'max:50'],
            'www' => ['required', 'max:50'],
        ];
    }
    public function messages() {
        return [
            'required'  => 'Pole :attribute jest wymagane.',
            'max'  => 'Nie więcej niż 50 znaków.',
        ];
    }

    public function attributes() {
        return [
            'nazwa' => 'Nazwa',
            'ulica' => 'Ulica',
            'miasto' => 'Miasto',
            'www' => 'WWW',
        ];
    }
}
