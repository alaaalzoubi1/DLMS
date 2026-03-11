<?php

namespace App\Http\Requests\Zatca;

use Illuminate\Foundation\Http\FormRequest;

class ZatcaOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'otp' => ['required', 'string'],

        ];
    }


}
