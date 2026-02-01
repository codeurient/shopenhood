<?php

namespace App\Http\Requests;

use App\Models\ProductVariation;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'listing_id' => ['required', 'exists:listings,id'],
            'variation_id' => [
                'nullable',
                'exists:product_variations,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $this->validateStockAvailability($value, $fail);
                    }
                },
            ],
            'quantity' => ['required', 'integer', 'min:1'],
            'shipping_address_id' => ['required', 'exists:addresses,id'],
            'billing_address_id' => ['nullable', 'exists:addresses,id'],
            'payment_method' => ['required', 'string', 'max:100'],
            'buyer_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Validate stock availability
     */
    protected function validateStockAvailability($variationId, $fail): void
    {
        $variation = ProductVariation::find($variationId);

        if (! $variation) {
            $fail('The selected product variation is no longer available.');

            return;
        }

        // Check if variation is active
        if (! $variation->is_active) {
            $fail('This product variation is currently unavailable.');

            return;
        }

        // Skip stock check if stock management is disabled
        if (! $variation->manage_stock) {
            return;
        }

        $requestedQuantity = $this->input('quantity', 1);

        // Check stock availability
        if ($variation->stock_quantity < $requestedQuantity) {
            if ($variation->allow_backorder) {
                // Allow backorder but inform user
                return;
            }

            if ($variation->stock_quantity <= 0) {
                $fail('This product is out of stock.');
            } else {
                $fail("Only {$variation->stock_quantity} items available in stock.");
            }
        }

        // Check low stock threshold
        if ($variation->isLowStock() && $variation->stock_quantity >= $requestedQuantity) {
            // Just a warning, don't fail validation
            // Could be handled in the controller to notify user
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'listing_id.required' => 'Please select a product.',
            'listing_id.exists' => 'The selected product is no longer available.',
            'quantity.required' => 'Please specify a quantity.',
            'quantity.min' => 'Quantity must be at least 1.',
            'shipping_address_id.required' => 'Please select a shipping address.',
            'payment_method.required' => 'Please select a payment method.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'variation_id' => 'product variation',
            'shipping_address_id' => 'shipping address',
            'billing_address_id' => 'billing address',
        ];
    }
}
