<?php

namespace App\Http\Requests;

use App\Enums\ImpressionType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateOrderRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'doctor_id'     => 'sometimes|integer|exists:doctors,id',
            'status'        => 'sometimes|in:pending,completed,cancelled',
            'type_id'       => 'sometimes|exists:types,id',
            'paid'          => 'sometimes|integer|min:0',
            'patient_name'  => 'sometimes|string|max:255',
            'receive'       => 'sometimes|date_format:Y-m-d H:i:s',
            'delivery'      => 'sometimes|date_format:Y-m-d H:i:s',
            'patient_id'    => 'sometimes|string|max:255',
            'impression_type' => ['sometimes', new Enum(ImpressionType::class)],
            'products'                                      => 'sometimes|array',
            'products.*.product_id'                         => 'sometimes|integer|exists:products,id',
            'products.*.tooth_color_id'                     => 'sometimes|integer|exists:tooth_colors,id',
            'products.*.tooth_numbers'                      => 'sometimes|array|min:1',
            'products.*.tooth_numbers.*'                    => 'sometimes|string|max:2',
            'products.*.specialization_subscriber_id'       => 'sometimes|integer|exists:specialization__subscribers,id',
            'products.*.note'                               => 'sometimes|string',
        ];
    }
}
