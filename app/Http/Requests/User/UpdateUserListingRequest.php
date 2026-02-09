<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserListingRequest extends FormRequest
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
            'discount_price' => 'nullable|numeric|min:0',
            'discount_start_date' => 'nullable|date|required_with:discount_price',
            'discount_end_date' => 'nullable|date|after:discount_start_date|required_with:discount_price',
            'availability_type' => 'nullable|in:in_stock,available_by_order',
            'has_delivery' => 'nullable|boolean',
            'has_domestic_delivery' => 'nullable|boolean',
            'domestic_delivery_price' => 'nullable|numeric|min:0',
            'has_international_delivery' => 'nullable|boolean',
            'international_delivery_price' => 'nullable|numeric|min:0',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'store_name' => 'nullable|string|max:255',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'detail_images' => 'nullable|array|max:10',
            'detail_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:listing_images,id',
            'variants' => 'nullable|array',
            'variations' => 'nullable|array',
            'variations.*.id' => 'nullable|integer',
            'variations.*.sku' => 'required_with:variations|string|max:100',
            'variations.*.attributes' => 'nullable|array',
            'variations.*.price' => 'required_with:variations|numeric|min:0',
            'variations.*.discount_price' => 'nullable|numeric|min:0',
            'variations.*.stock_quantity' => 'nullable|integer|min:0',
            'variations.*.is_default' => 'nullable|boolean',
            'variations.*.is_active' => 'nullable|boolean',
            'variations.*.images' => 'nullable|array',
            'variations.*.images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'variations.*.price_tiers' => 'nullable|array',
            'variations.*.price_tiers.*.min_quantity' => 'required_with:variations.*.price_tiers|integer|min:1',
            'variations.*.price_tiers.*.max_quantity' => 'nullable|integer|min:1',
            'variations.*.price_tiers.*.unit_price' => 'required_with:variations.*.price_tiers|numeric|min:0.01',

            // Wholesale fields
            'is_wholesale' => 'nullable|boolean',
            'wholesale_min_order_qty' => 'nullable|required_if:is_wholesale,1|integer|min:1',
            'wholesale_qty_increment' => 'nullable|integer|min:1',
            'wholesale_lead_time_days' => 'nullable|integer|min:0|max:365',
            'wholesale_sample_available' => 'nullable|boolean',
            'wholesale_sample_price' => 'nullable|numeric|min:0',
            'wholesale_terms' => 'nullable|string|max:2000',

            // SEO fields (business users only)
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
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
            'meta_title.max' => 'Meta title must not exceed 60 characters for optimal search display.',
            'meta_description.max' => 'Meta description must not exceed 160 characters for optimal search display.',
        ];
    }
}
