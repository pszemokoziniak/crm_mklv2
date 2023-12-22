<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FutureProjectRequest extends FormRequest
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
            'nazwa' => ['required'],
            'miasto' => ['required'],
            'kraj_id' => ['required'],
            'objekt_id' => ['required'],
            'client_id' => ['required'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date'],
            'opis' => ['nullable'],
            'inwestor' => ['required'],
            'dane_kontaktowe' => ['nullable'],
            'data_kontakt' => ['nullable'],
            'faza_id' => ['nullable'],
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
