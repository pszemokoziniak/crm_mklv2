<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KursyStoreRequest extends FormRequest
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
            'name' => ['required'],
            'kurs' => ['required', 'numeric']
        ];
    }
    public function messages() {
        return [
            'required'  => 'Pole :attribute jest wymagane.',
            'max' => 'Nazwa możne zawierać',
            'numeric' => 'Pole musi zawierać cyfrę',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nazwa',
            'kurs' => 'Kurs',
        ];
    }
}
