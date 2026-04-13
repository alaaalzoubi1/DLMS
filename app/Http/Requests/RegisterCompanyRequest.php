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
        return [

            'company_name' => 'required|string|max:255',
            'country_code' => 'required|in:SA,SY',
            'tax_number' => [
                'nullable',
                'string',
                'digits:15',
                'starts_with:3',
                'ends_with:3',
                Rule::unique('subscribers', 'tax_number'),
            ],

            'commercial_registration' => [
                'nullable',
                'string',
                'max:50',
                'unique:subscribers,commercial_registration',
            ],

            'street' => 'nullable|string|max:255',
            'building_number' => 'nullable|string|max:50',
            'additional_number' => 'nullable|string|max:50',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',

            'locationAddress' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z]{4}[0-9]{4}$/'
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
            'locationAddress.regex' => 'Location address must be 4 letters followed by 4 digits (example: RRRD2929).',
        ];
    }
}
