<?php

namespace App\Http\Requests\User;

use App\Models\ListingType;
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
        $typeSlug = null;
        if ($this->listing_type_id) {
            $typeSlug = ListingType::find($this->listing_type_id)?->slug;
        }

        $rules = [
            'listing_type_id' => 'required|exists:listing_types,id',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'currency' => 'nullable|string|max:3',
            'condition' => 'required|in:new,used,refurbished',
            'refurbished_description' => 'required_if:condition,refurbished|nullable|string|max:2000',
            'location_id' => 'nullable|exists:locations,id',

            // Images
            'product_images' => 'nullable|array|max:10',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:listing_images,id',
        ];

        // --- Sell ---
        if ($typeSlug === 'sell') {
            $rules['base_price'] = 'required|numeric|min:0.01';
            $rules['is_negotiable'] = 'nullable|boolean';
            $rules['discount_price'] = 'nullable|numeric|min:0';
            $rules['discount_start_date'] = 'nullable|date|required_with:discount_price';
            $rules['discount_end_date'] = 'nullable|date|after:discount_start_date|required_with:discount_price';
        }

        // --- Buy ---
        if ($typeSlug === 'buy') {
            $rules['budget_max'] = 'nullable|numeric|min:0';
        }

        // --- Barter ---
        if ($typeSlug === 'barter') {
            $rules['barter_exchange_for'] = 'required|string|max:1000';
        }

        // --- Auction ---
        if ($typeSlug === 'auction') {
            $rules['base_price'] = 'required|numeric|min:0.01';
            $rules['auction_end_date'] = 'required|date|after:now';
            $rules['auction_reserve_price'] = 'nullable|numeric|min:0';
            $rules['auction_bid_increment'] = 'nullable|numeric|min:0.01';
        }

        // --- Delivery: all types except Buy ---
        if ($typeSlug !== 'buy') {
            $rules['has_delivery'] = 'nullable|boolean';
            $rules['has_same_city_delivery'] = 'nullable|boolean';
            $rules['same_city_delivery_price'] = 'nullable|numeric|min:0';
            $rules['same_city_delivery_days'] = 'nullable|integer|min:1|max:365';
            $rules['has_domestic_delivery'] = 'nullable|boolean';
            $rules['domestic_delivery_price'] = 'nullable|numeric|min:0';
            $rules['domestic_delivery_days'] = 'nullable|integer|min:1|max:365';
            $rules['has_international_delivery'] = 'nullable|boolean';
            $rules['international_delivery_price'] = 'nullable|numeric|min:0';
            $rules['international_delivery_days'] = 'nullable|integer|min:1|max:365';
        }

        return $rules;
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
            'condition.required' => 'Please select the item condition.',
            'condition.in' => 'Item condition must be either new or second-hand.',
            'base_price.required' => 'Please provide a price for your listing.',
            'base_price.min' => 'Price must be greater than 0.',
            'barter_exchange_for.required' => 'Please describe what you want in exchange.',
            'auction_end_date.required' => 'Please set an end date for the auction.',
            'auction_end_date.after' => 'The auction end date must be in the future.',
            'product_images.max' => 'You can upload a maximum of 10 images.',
            'product_images.*.max' => 'Each image must be less than 5MB.',
        ];
    }
}
