<?php

namespace App\Http\Requests\User;

use App\Models\Listing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0.01',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'applicable_to' => 'required|in:all,categories,listings',
            'restrictions' => 'nullable|array',
            'restrictions.*' => ['integer', $this->restrictionExistsRule()],
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'applicable_to.in' => 'The applicable to field must be all, categories, or listings.',
            'restrictions.*.exists' => 'One or more selected restrictions do not belong to your listings.',
        ];
    }

    /**
     * Ensure restriction IDs belong to the authenticated user's data.
     */
    private function restrictionExistsRule(): ?Rule
    {
        $applicableTo = $this->input('applicable_to');
        $userId = auth()->id();

        if ($applicableTo === 'listings') {
            return Rule::exists('listings', 'id')->where('user_id', $userId);
        }

        if ($applicableTo === 'categories') {
            $categoryIds = Listing::where('user_id', $userId)
                ->whereIn('status', ['approved', 'active'])
                ->distinct()
                ->pluck('category_id');

            return Rule::exists('categories', 'id')->whereIn('id', $categoryIds->toArray());
        }

        return null;
    }
}
