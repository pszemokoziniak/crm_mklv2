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
        // Usunięto 'id_zapyt' => ['required'] ponieważ jest teraz
        // generowane podczas tworzenia automatycznie na backendzie
        // podczas update jest to i tak wyłączone z update() lub powinno
        // być pomijane jeśli nie przesyłamy go w body.
        // Ewentualnie id_zapyt mogło zostać tylko do odczytu.

        $rules = [
            'user_otrzymal_id' => ['required'],
            'data_otrzymania' => ['required', 'date'],
            'data_zlozenia' => ['required', 'date'],
            'client_id' => ['required'],
            'nazwa_projektu' => ['required', 'max:100'],
            'miejscowosc' => ['required', 'max:50'],
            'kraj_id' => ['required'],
            'zakres_id' => ['required'],
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
            'kwota' => ['required', 'numeric'],
            'waluta_id' => ['required'],
            'opis' => ['required', 'max:5000'],
        ];

        // Przy update nadal może przychodzić id_zapyt i możemy chcieć to zwalidować,
        // ale de facto nie jest to zmieniane. Zostawiamy 'id_zapyt' jako nullable lub pomijamy
        if ($this->isMethod('put') || $this->isMethod('patch')) {
             $rules['id_zapyt'] = ['required'];
        }

        return $rules;
    }

    public function messages() {
        return [
            'required'  => 'Pole jest wymagane.',
            'unique' => 'Nazwa użyta',
            'numeric' => 'Pole może zawierać tylko cyfry',
            'max' => 'Pole :attribute nie może być dłuższe niż :max znaków.',
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
            'opis' => 'Opis projektu',
            'nazwa_projektu' => 'Nazwa projektu',
            'miejscowosc' => 'Miejscowość',
        ];
    }
}
