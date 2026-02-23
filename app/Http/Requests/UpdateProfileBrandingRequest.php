<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileBrandingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isBusinessUser() && auth()->user()->hasBusinessProfile();
    }

    public function rules(): array
    {
        return [
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.image' => 'Logo must be an image file.',
            'logo.max' => 'Logo file size cannot exceed 2MB.',
            'banner.image' => 'Banner must be an image file.',
            'banner.max' => 'Banner file size cannot exceed 4MB.',
        ];
    }
}
