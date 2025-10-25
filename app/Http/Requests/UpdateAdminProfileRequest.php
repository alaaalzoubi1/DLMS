<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // فيك لاحقًا تضيف شرط: return auth('admin')->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $userId = auth('admin')->id();
        $subscriberId = auth('admin')->user()->subscribers?->id;

        return [
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'is_available' => ['sometimes', 'boolean'],

            'subscriber.name' => ['sometimes', 'string', 'max:255'],
            'subscriber.company_code' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('subscribers', 'company_code')->ignore($subscriberId),
            ],
        ];
    }
}
