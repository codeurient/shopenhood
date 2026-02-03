<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserListingRequest extends FormRequest
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
            'listing_type_id' => 'required|exists:listing_types,id',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'base_price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'is_negotiable' => 'nullable|boolean',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'store_name' => 'nullable|string|max:255',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'detail_images' => 'nullable|array|max:10',
            'detail_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'variants' => 'nullable|array',
            'variations' => 'nullable|array',
            'variations.*.sku' => 'required_with:variations|string|max:100',
            'variations.*.attributes' => 'nullable|array',
            'variations.*.price' => 'required_with:variations|numeric|min:0',
            'variations.*.discount_price' => 'nullable|numeric|min:0',
            'variations.*.stock_quantity' => 'nullable|integer|min:0',
            'variations.*.is_default' => 'nullable|boolean',
            'variations.*.is_active' => 'nullable|boolean',
            'variations.*.images' => 'nullable|array',
            'variations.*.images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a title for your listing.',
            'description.required' => 'Please describe your listing.',
            'category_id.required' => 'Please select a category.',
            'listing_type_id.required' => 'Please select a listing type.',
            'detail_images.max' => 'You can upload a maximum of 10 detail images.',
            'detail_images.*.max' => 'Each image must be less than 5MB.',
            'main_image.max' => 'The main image must be less than 5MB.',
        ];
    }
}
