<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isBusinessUser();
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
            'condition' => 'required|in:new,used',
            'store_name' => 'nullable|string|max:255',
            'availability_type' => 'nullable|in:in_stock,available_by_order',
            'has_delivery' => 'nullable|boolean',
            'has_domestic_delivery' => 'nullable|boolean',
            'domestic_delivery_price' => 'nullable|numeric|min:0',
            'has_international_delivery' => 'nullable|boolean',
            'international_delivery_price' => 'nullable|numeric|min:0',
            'location_id' => 'nullable|exists:locations,id',

            // Non-main-shown variant attribute selections
            'variant_attributes' => 'nullable|array',
            'variant_attributes.*' => 'nullable|integer',

            'variants' => 'nullable|array',
            'variations' => 'nullable|array',
            'variations.*.sku' => 'required_with:variations|string|max:100',
            'variations.*.attributes' => 'nullable|array',
            'variations.*.price' => 'required_with:variations|numeric|min:0',
            'variations.*.discount_price' => 'nullable|numeric|min:0',
            'variations.*.discount_start_date' => 'nullable|date',
            'variations.*.discount_end_date' => 'nullable|date|after_or_equal:variations.*.discount_start_date',
            'variations.*.stock_quantity' => 'nullable|integer|min:0',
            'variations.*.is_default' => 'nullable|boolean',
            'variations.*.is_active' => 'nullable|boolean',
            'variations.*.images' => 'nullable|array',
            'variations.*.images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'variations.*.price_tiers' => 'nullable|array',
            'variations.*.price_tiers.*.min_quantity' => 'required_with:variations.*.price_tiers|integer|min:1',
            'variations.*.price_tiers.*.max_quantity' => 'nullable|integer|min:1',
            'variations.*.price_tiers.*.unit_price' => 'required_with:variations.*.price_tiers|numeric|min:0.01',

            // Per-variation wholesale
            'variations.*.is_wholesale' => 'nullable|boolean',
            'variations.*.wholesale_min_order_qty' => 'nullable|integer|min:1',
            'variations.*.wholesale_qty_increment' => 'nullable|integer|min:1',
            'variations.*.wholesale_lead_time_days' => 'nullable|integer|min:0|max:365',
            'variations.*.wholesale_sample_available' => 'nullable|boolean',
            'variations.*.wholesale_sample_price' => 'nullable|numeric|min:0',
            'variations.*.wholesale_terms' => 'nullable|string|max:2000',

            // SEO fields
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
            'condition.required' => 'Please select the product condition.',
            'condition.in' => 'Product condition must be either new or second-hand.',
            'meta_title.max' => 'Meta title must not exceed 60 characters for optimal search display.',
            'meta_description.max' => 'Meta description must not exceed 160 characters for optimal search display.',
        ];
    }
}
