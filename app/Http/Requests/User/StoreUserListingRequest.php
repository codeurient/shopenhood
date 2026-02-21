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
            'base_price' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|max:3',
            'is_negotiable' => 'nullable|boolean',
            'condition' => 'required|in:new,used',

            // Top-level discount
            'discount_price' => 'nullable|numeric|min:0',
            'discount_start_date' => 'nullable|date|required_with:discount_price',
            'discount_end_date' => 'nullable|date|after:discount_start_date|required_with:discount_price',

            'has_delivery' => 'nullable|boolean',
            'has_domestic_delivery' => 'nullable|boolean',
            'domestic_delivery_price' => 'nullable|numeric|min:0',
            'has_international_delivery' => 'nullable|boolean',
            'international_delivery_price' => 'nullable|numeric|min:0',
            'location_id' => 'nullable|exists:locations,id',

            // Images: first image is primary
            'product_images' => 'nullable|array|max:10',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'detail_images' => 'nullable|array|max:10',
            'detail_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',

            // Top-level wholesale
            'is_wholesale' => 'nullable|boolean',
            'wholesale_min_order_qty' => 'nullable|required_if:is_wholesale,1|integer|min:1',
            'wholesale_qty_increment' => 'nullable|integer|min:1',
            'wholesale_lead_time_days' => 'nullable|integer|min:0|max:365',
            'wholesale_sample_available' => 'nullable|boolean',
            'wholesale_sample_price' => 'nullable|numeric|min:0',
            'wholesale_terms' => 'nullable|string|max:2000',
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
            'base_price.required' => 'Please provide a price for your listing.',
            'base_price.min' => 'Price must be greater than 0.',
            'product_images.max' => 'You can upload a maximum of 10 images.',
            'product_images.*.max' => 'Each image must be less than 5MB.',
            'detail_images.max' => 'You can upload a maximum of 10 detail images.',
            'detail_images.*.max' => 'Each image must be less than 5MB.',
            'main_image.max' => 'The main image must be less than 5MB.',
        ];
    }
}
