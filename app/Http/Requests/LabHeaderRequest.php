<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LabHeaderRequest extends FormRequest
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
            'lab_name_en' => 'required|string|max:255',
            'lab_name_ar' => 'required|string|max:255',
            'logo'    => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'address' => 'required|string|max:500'
        ];
    }

}
