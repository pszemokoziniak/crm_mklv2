<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ZadaniaStoreRequest extends FormRequest
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
            'responsible_person_id' => ['required'],
            'deadline' => ['required', 'date'],
            'subject' => ['required'],
            'description' => ['required'],
            'user_id' => ['required'],
        ];
    }

    public function messages() {
        return [
            'required'  => 'Pole jest wymagane.',
            'unique' => 'Nazwa użyta',
            'numeric' => 'Pole może zawierać tylko cyfry',
        ];
    }

    public function attributes()
    {
        return [
            'zapytanie_id' => 'Zapytanie',
            'typs' => 'Typ klienta',
            'client_id' => 'Klient',
            'data_wyslania' => 'Ofertę wysłano',
            'kwota' =>  'Kwota',
            'waluta' => 'Waluta',
            'data_kontakt' => 'Data kontaktu',
            'oferta_status_id' => 'Status',
            'opis' => 'Opis',
        ];
    }
}
