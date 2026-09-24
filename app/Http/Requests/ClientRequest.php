<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
            'nazwa' => ['required', 'max:200', function ($attribute, $value, $fail) {
                // Blokada duplikatu firmy — sprawdzamy tez Archiwum.
                if ($duplicate = Client::findDuplicate($value)) {
                    $fail(Client::duplicateMessage($duplicate));
                }
            }],
            'user_id' => ['required'],
            'kraj_id' => ['required'],
            'branza_id' => ['required'],
        ];
    }
    public function messages() {
        return [
            'required'  => 'Pole jest wymagane.',
            'unique' => 'Nazwa użyta',
            'numeric' => 'Pole może zawierać tylko cyfry',
        ];
    }
}
