<?php

namespace App\Http\Requests\Admin;

use App\Models\Policy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'policy_type' => ['required', 'string', Rule::in(array_keys(Policy::POLICY_TYPES))],
            'version' => ['required', 'string', 'max:20'],
            'bump_version' => ['boolean'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'require_acceptance' => ['boolean'],
            'show_in_footer' => ['boolean'],
            'show_during_registration' => ['boolean'],
            'show_during_checkout' => ['boolean'],
            'show_during_listing' => ['boolean'],
            'platform_actions' => ['nullable', 'array'],
            'platform_actions.*' => ['string', Rule::in(array_keys(Policy::PLATFORM_ACTIONS))],
        ];
    }
}
