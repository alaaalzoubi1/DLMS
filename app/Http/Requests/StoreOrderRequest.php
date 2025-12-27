<?php

namespace App\Http\Requests;

use App\Enums\ImpressionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
            'patient_id'      => 'nullable|string|max:50',
            'impression_type' => ['required', new Enum(ImpressionType::class)],

            'products'                        => 'required|array|min:1',

            'products.*.product_id'           => 'required|exists:products,id',
            'products.*.tooth_color_id'       => 'required|exists:tooth_colors,id',

            'products.*.tooth_numbers'        => 'required|array|min:1',

            'products.*.tooth_numbers.*'      => 'required|string|max:2',

            'products.*.note'                 => 'nullable|string',
        ];

    }

}
