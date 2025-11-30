<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrdersWithFilters extends FormRequest
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
            'from'          => ['nullable', 'date'],
            'to'            => ['nullable', 'date', 'after_or_equal:from'],
            'status'        => ['nullable', 'in:pending,completed,cancelled'],
            'invoiced'      => ['nullable', 'boolean'],
            'type_id'       => ['nullable', 'integer', 'exists:types,id'],
            'subscriber_id' => ['nullable', 'integer', 'exists:subscribers,id'],
            'patient_name'   => ['nullable', 'string', 'regex:/^[\p{L}\p{N}\s_-]+$/u', 'max:255'],
            'patient_id'     => ['nullable', 'string', 'regex:/^[\p{L}\p{N}\s_-]+$/u', 'max:255'],
            'doctor_id'    => ['nullable','integer','exists:doctors,id']
        ];
    }

    public function messages(): array
    {
        return [
            'to.after_or_equal' => 'The to date must be after or equal to the from date.',
            'status.in'         => 'Status must be one of: pending, completed, cancelled.',
        ];
    }
}
