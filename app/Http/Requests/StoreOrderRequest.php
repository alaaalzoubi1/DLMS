<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'subscriber_id'   => 'required|exists:subscribers,id',
            'type_id'         => 'required|exists:types,id',
            'patient_name'    => 'required|string|max:50',
            'patient_id'      => 'required|string|max:50',

            'products'                        => 'required|array|min:1',
            'products.*.product_id'           => 'required|exists:products,id',
            'products.*.tooth_color_id'       => 'required|exists:tooth_colors,id',
            'products.*.tooth_number'         => 'required|string|max:2',
            'products.*.note'                 => 'nullable|string',
        ];
    }

}
