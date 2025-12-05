<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClinicHeaderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'clinic_name_en' => 'required|string|max:255',
            'clinic_name_ar' => 'required|string|max:255',
            'address_ar' => 'required|string|max:500',
            'address_en' => 'required|string|max:500',
            'logo'    => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

}
