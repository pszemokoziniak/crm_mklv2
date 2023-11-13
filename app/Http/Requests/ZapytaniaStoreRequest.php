<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ZapytaniaStoreRequest extends FormRequest
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
        'id_zapyt' => ['required'],
        'user_otrzymal_id' => ['required'],
        'data_otrzymania' => ['required', 'date'],
        'data_zlozenia' => ['required', 'date'],
        'client_id' => ['required'],
        'nazwa_projektu' => ['required', 'max:100'],
        'miejscowosc' => ['required', 'max:50'],
        'kraj_id' => ['required'],
        'zakres_id' => ['required'],
//        'user_id' => ['required'],
        'start' => ['required', 'date'],
        'end' => ['required', 'date'],
        'kwota' => ['required', 'numeric'],
        'waluta' => ['required'],
        'opis' => ['required', 'max:50'],
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
            'dzialanie' => 'Działanie',
            'grupa_docelowa' => 'Grupa docelowa',
            'dzialania_koordynacyjne' => 'Działania koordynacyjne',
            'tekst' => 'Opis działania',
            'data_start' =>  'Data od',
            'data_end' => 'Data do',
            'planowany_budzet' => 'Planowany budżet',
            'tekst1' => 'Liczba osób',
        ];
    }
}
