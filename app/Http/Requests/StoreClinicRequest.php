<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClinicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subscriber = auth('admin')->user()->subscribers;
        $isSaudi = $subscriber->country_code === 'SA';

        return [
            'name' => ['required', 'string', 'max:255'],

            'tax_number' => [
                Rule::requiredIf($isSaudi),
                'string',
                'max:20',
                'unique:clinics,tax_number',
                Rule::when($isSaudi, [
                    'starts_with:3',
                    'ends_with:3'
                ]),
            ],

            'commercial_registration' => [
                Rule::requiredIf($isSaudi),
                'string',
                'max:50',
                'unique:clinics,commercial_registration',
            ],

            'street' => [Rule::requiredIf($isSaudi), 'string', 'max:255'],
            'building_number' => [Rule::requiredIf($isSaudi), 'string', 'max:50'],
            'additional_number' => ['nullable', 'string', 'max:50'],
            'district' => [Rule::requiredIf($isSaudi), 'string', 'max:255'],
            'city' => [Rule::requiredIf($isSaudi),  'string', 'max:255'],
            'postal_code' => [Rule::requiredIf($isSaudi), 'string', 'max:20'],

            'locationAddress' => [
                Rule::requiredIf($isSaudi),
                'string',
                'regex:/^[A-Za-z]{4}[0-9]{4}$/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'locationAddress.regex' =>
                'Location address must start with 4 letters followed by 4 digits (e.g. ABCD1234).',

            'tax_number.starts_with' =>
                'Saudi VAT number must start with 3.',

            'tax_number.ends_with' =>
                'Saudi VAT number must end with 3.',
        ];
    }
}
