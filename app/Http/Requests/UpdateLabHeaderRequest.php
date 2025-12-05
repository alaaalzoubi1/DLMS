<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLabHeaderRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'lab_name_ar' => 'sometimes|string|max:255',
            'lab_name_en' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:500',
            'logo' => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:2048'
        ];
    }
}
