<?php

namespace App\Http\Requests;

use App\Models\ReminderRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReminderRuleStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'event' => ['required', Rule::in(array_keys(ReminderRule::EVENTS))],
            'days_before' => ['required', 'integer', 'between:0,365'],
            'recipients' => ['required', 'array', 'min:1'],
            'recipients.*' => ['string', 'regex:/^(opiekun|opracowuje|user:\d+)$/'],
            'subject' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'active' => ['boolean'],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Pole jest wymagane.',
            'recipients.min' => 'Wybierz przynajmniej jednego odbiorcę.',
            'recipients.*.regex' => 'Nieprawidłowy odbiorca.',
            'between' => 'Wartość musi być w zakresie :min–:max.',
            'in' => 'Nieprawidłowa wartość.',
        ];
    }
}
