<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isSaudi = $this->country_code === 'SA';

        return [

            'company_name' => 'required|string|max:255',
            'country_code' => 'required|in:SA,SY',

            'tax_number' => [
                Rule::requiredIf($isSaudi),
                Rule::when($isSaudi, [
                    'string',
                    'digits:15',
                    'starts_with:3',
                    'ends_with:3',
                ]),
                Rule::unique('subscribers', 'tax_number'),
            ],

            'commercial_registration' => [
                $isSaudi ? 'required' : 'nullable',
                'string',
                'max:50',
                'unique:subscribers,commercial_registration',
            ],

            'street' => $isSaudi ? 'required|string|max:255' : 'nullable|string|max:255',
            'building_number' => $isSaudi ? 'required|string|max:50' : 'nullable|string|max:50',
            'additional_number' => 'nullable|string|max:50',
            'district' =>  'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => $isSaudi ? 'required|string|max:20' : 'nullable|string|max:20',
            'locationAddress' => [
                Rule::requiredIf($isSaudi),
                'string',
                'max:255',
                Rule::when($isSaudi, [
                    'regex:/^[A-Za-z]{4}[0-9]{4}$/'
                ]),
            ],
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'fcm_token' => 'nullable|string|max:500',
        ];
    }
    public function messages(): array
    {
        return [
            'locationAddress.required' => 'Location address is required for Saudi companies.',
            'locationAddress.regex' => 'Location address must be 4 letters followed by 4 digits (example: RRRD2929).',
        ];
    }
}
