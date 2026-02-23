<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isBusinessUser();
    }

    public function rules(): array
    {
        return [
            'description' => 'nullable|string|max:2000',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state_province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country_id' => 'nullable|exists:locations,id',
            'business_email' => 'nullable|email|max:255',
            'business_phone' => 'nullable|string|max:30',
            'website' => 'nullable|url|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'website.url' => 'Please enter a valid website URL.',
            'business_email.email' => 'Please enter a valid email address.',
        ];
    }
}
