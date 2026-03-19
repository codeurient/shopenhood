@extends('admin.layouts.app')

@section('title', 'Adjust Stock - ' . $variation->sku)
@section('page-title', 'Adjust Stock')

@section('content')
<div>

    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.stock.index') }}"
           class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#1A1A1A] mb-2 transition">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to Stock Management
        </a>
        <h2 class="text-2xl font-bold text-[#1A1A1A]">Adjust Stock — {{ $variation->sku }}</h2>
        <p class="text-[#37474F] text-sm mt-1">{{ $variation->listing->title }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Stock Adjustment Form -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-[#D4AF37]"></i>
                        Adjust Stock
                    </h3>
                </div>

                <div class="p-5">
                    <form method="POST" action="{{ route('admin.stock.adjust', $variation) }}">
                        @csrf

                        <div class="space-y-5">

                            <!-- Transaction Type -->
                            <div>
                                <label for="type" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                    Transaction Type <span class="text-red-500">*</span>
                                </label>
                                <select name="type" id="type" required
                                        class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('type') border-red-500 @else border-[#E0E0E0] @enderror">
                                    <option value="">Select type...</option>
                                    <option value="purchase">Purchase (Add Stock)</option>
                                    <option value="sale">Sale (Remove Stock)</option>
                                    <option value="return">Return (Add Stock)</option>
                                    <option value="adjustment">Adjustment</option>
                                    <option value="damage">Damage/Loss (Remove Stock)</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label for="quantity" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                    Quantity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="quantity" id="quantity" min="1" required
                                       placeholder="Enter quantity"
                                       class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('quantity') border-red-500 @else border-[#E0E0E0] @enderror">
                                <p class="mt-1 text-[12px] text-[#37474F]">
                                    Current stock: <span class="font-semibold text-[#1A1A1A]">{{ $variation->stock_quantity }}</span> units
                                </p>
                                <p class="mt-1 text-[12px] text-[#37474F]">
                                    Quantity will be automatically added or removed based on the transaction type.
                                </p>
                                @error('quantity')
                                    <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reference -->
                            <div>
                                <label for="reference" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                    Reference <span class="text-[12px] font-normal text-[#37474F]">(Order #, Invoice #, etc.)</span>
                                </label>
                                <input type="text" name="reference" id="reference" placeholder="PO-12345"
                                       class="h-[34px] border text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] block w-full px-3 @error('reference') border-red-500 @else border-[#E0E0E0] @enderror">
                                @error('reference')
                                    <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block mb-1.5 text-[13px] font-medium text-[#1A1A1A]">
                                    Notes
                                </label>
                                <textarea name="notes" id="notes" rows="3"
                                          placeholder="Additional notes about this stock adjustment..."
                                          class="block p-3 w-full text-[13px] text-[#1A1A1A] rounded border focus:ring-0 focus:border-[#D4AF37] @error('notes') border-red-500 @else border-[#E0E0E0] @enderror"></textarea>
                                @error('notes')
                                    <p class="mt-1 text-[12px] text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <a href="{{ route('admin.stock.index') }}"
                               class="inline-flex items-center justify-center h-[34px] px-4 text-[13px] font-medium text-[#37474F] bg-white border border-[#E0E0E0] rounded hover:bg-gray-50 transition">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 h-[34px] px-4 text-[13px] font-semibold text-[#000000] bg-[#D4AF37] rounded hover:brightness-110 transition">
                                <i class="fa-solid fa-check text-xs"></i>
                                Adjust Stock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-5">

            <!-- Product Info -->
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-box text-[#D4AF37]"></i>
                        Product Info
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">SKU</dt>
                        <dd class="mt-0.5 text-[13px] text-[#1A1A1A] font-medium">{{ $variation->sku }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Product</dt>
                        <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $variation->listing->title }}</dd>
                    </div>
                    @if($variation->attributes->count())
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Variant</dt>
                        <dd class="mt-1 flex flex-wrap gap-1">
                            @foreach($variation->attributes as $attr)
                                <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-medium bg-gray-100 text-[#37474F]">
                                    {{ $attr->variant->name }}: {{ $attr->variantItem->value }}
                                </span>
                            @endforeach
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Price</dt>
                        <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">${{ number_format($variation->price, 2) }}</dd>
                    </div>
                    <div class="pt-3 border-t border-[#E0E0E0]">
                        <dt class="text-[12px] font-medium text-[#37474F]">Current Stock</dt>
                        <dd class="mt-0.5 text-2xl font-bold text-[#1A1A1A]">{{ $variation->stock_quantity }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Low Stock Threshold</dt>
                        <dd class="mt-0.5 text-[13px] text-[#1A1A1A]">{{ $variation->low_stock_threshold }}</dd>
                    </div>
                    <div>
                        <dt class="text-[12px] font-medium text-[#37474F]">Status</dt>
                        <dd class="mt-1">
                            @if($variation->stock_quantity <= 0)
                                <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#C0392B]/10 text-[#C0392B]">Out of Stock</span>
                            @elseif($variation->stock_quantity <= $variation->low_stock_threshold)
                                <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-yellow-50 text-yellow-700">Low Stock</span>
                            @else
                                <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#D4AF37]/20 text-[#D4AF37]">In Stock</span>
                            @endif
                        </dd>
                    </div>
                </div>
            </div>

            <!-- Recent Stock Movements -->
            <div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#E0E0E0]">
                    <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-[#D4AF37]"></i>
                        Recent Movements
                    </h3>
                </div>
                <div class="p-4">
                    @if($variation->stockMovements->count() > 0)
                        <div class="space-y-3">
                            @foreach($variation->stockMovements as $movement)
                            <div class="border-l-[3px] pl-3 {{ $movement->isIncrease() ? 'border-green-500' : 'border-[#C0392B]' }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-[13px] font-medium text-[#1A1A1A]">{{ ucfirst($movement->type) }}</p>
                                        <p class="text-[11px] text-[#37474F]">{{ $movement->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                    <span class="text-[13px] font-semibold {{ $movement->isIncrease() ? 'text-green-600' : 'text-[#C0392B]' }}">
                                        {{ $movement->isIncrease() ? '+' : '' }}{{ $movement->quantity_change }}
                                    </span>
                                </div>
                                @if($movement->reference)
                                    <p class="text-[11px] text-[#37474F] mt-0.5">Ref: {{ $movement->reference }}</p>
                                @endif
                                @if($movement->notes)
                                    <p class="text-[11px] text-[#37474F] mt-0.5">{{ $movement->notes }}</p>
                                @endif
                                <p class="text-[11px] text-[#37474F]/60 mt-0.5">
                                    {{ $movement->quantity_before }} → {{ $movement->quantity_after }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-[13px] text-[#37474F] text-center py-4">No movements yet</p>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
