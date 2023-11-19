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
            'linkedIn' => ['nullable', 'max:50'],
            'message' => ['required', 'max:10000'],
            'user_id' => ['required', 'max:36'],
            'kraj_id' => ['required', 'max:36'],
            'branza_id' => ['required', 'max:36'],
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
