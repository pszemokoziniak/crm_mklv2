<?php

namespace App\Http\Requests;

use App\Models\Client;
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
            'nazwa' => ['required', 'max:200', function ($attribute, $value, $fail) {
                $client = $this->route('client');

                // Sprawdzamy tylko przy zmianie nazwy — edycja innych pol klienta,
                // ktory juz ma "blizniaka" w bazie, nie powinna byc blokowana.
                if ($client && Client::normalizeName($value) === Client::normalizeName($client->nazwa)) {
                    return;
                }

                if ($duplicate = Client::findDuplicate($value, $client?->id)) {
                    $fail(Client::duplicateMessage($duplicate));
                }
            }],
            'ulica' => ['required', 'max:200'],
            'www' => ['nullable', 'max:200'],
            'linkedIn' => ['nullable', 'max:200'],
            'message' => ['required', 'max:10000'],
            'user_id' => ['required', 'max:36'],
            'kraj_id' => ['required', 'max:36'],
            'branza_id' => ['required', 'max:36'],
        ];
    }
    public function messages() {
        return [
            'required'  => 'Pole :attribute jest wymagane.',
            'max'  => 'Nie więcej niż :max znaków.',
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
