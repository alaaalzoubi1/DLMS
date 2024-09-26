<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'company_code' => 'required|string|exists:subscribers,company_code',
            'email' => 'required|email|unique:doctors_accounts,email',
            'password' => 'required|string|min:8',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ];
    }

}
