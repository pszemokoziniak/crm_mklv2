<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfertaStoreRequest extends FormRequest
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
            'typ' => ['required'],
            'client_id' => ['required'],
            'data_wyslania' => ['required', 'date'],
            'kwota' => ['required'],
            'waluta' => ['required', 'max:100'],
            'data_kontakt' => ['required', 'max:50'],
            'oferta_status_id' => ['required'],
            'opis' => ['required'],
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
