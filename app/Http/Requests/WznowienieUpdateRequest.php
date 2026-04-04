<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WznowienieUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust authorization logic as needed
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'text' => ['required', 'string'],
            'id_zapytania' => ['required', 'exists:zapytanias,id'],
            'id_user' => ['nullable', 'exists:users,id'],
            'data_otrzymania' => ['nullable', 'date'],
            'data_zlozenia' => ['nullable', 'date'],
            'preliminarz' => ['nullable', 'string', 'in:Tak,Nie'],
            'zakres_id' => ['nullable', 'exists:zakres,id'],
            'user_opracowuje_id' => ['nullable', 'exists:users,id'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date'],
            'kwota' => ['nullable', 'numeric'],
            'waluta_id' => ['nullable', 'exists:walutas,id'],
        ];
    }
}
