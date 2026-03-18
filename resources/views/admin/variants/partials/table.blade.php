<table class="w-full text-left border-collapse">
    <thead>
        <tr class="bg-[#37474F] border-b border-[#000000]/20">
            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Name</th>
            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Type</th>
            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Items</th>
            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Required</th>
            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Sort</th>
            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($variants as $variant)
        <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
            <td class="px-3 py-2.5 text-[13px]">
                <span class="font-medium text-[#1A1A1A]">{{ $variant->name }}</span>
                <br>
                <span class="text-[11px] text-[#37474F]">{{ $variant->slug }}</span>
            </td>
            <td class="px-3 py-2.5">
                @php
                    $typeColors = [
                        'select'   => 'bg-blue-50 text-blue-700',
                        'radio'    => 'bg-purple-50 text-purple-700',
                        'checkbox' => 'bg-green-50 text-green-700',
                        'text'     => 'bg-orange-50 text-orange-700',
                        'number'   => 'bg-pink-50 text-pink-700',
                    ];
                    $typeColor = $typeColors[$variant->type] ?? 'bg-gray-100 text-gray-600';
                @endphp
                <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold {{ $typeColor }}">
                    {{ ucfirst($variant->type) }}
                </span>
            </td>
            <td class="px-3 py-2.5">
                <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#D4AF37]/20 text-[#1A1A1A]">
                    {{ $variant->items_count }} items
                </span>
            </td>
            <td class="px-3 py-2.5">
                @if($variant->is_required)
                    <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-yellow-50 text-yellow-700">Required</span>
                @else
                    <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-gray-100 text-gray-500">Optional</span>
                @endif
            </td>
            <td class="px-3 py-2.5">
                @if($variant->is_active)
                    <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#D4AF37]/20 text-[#D4AF37]">Active</span>
                @else
                    <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#C0392B]/10 text-[#C0392B]">Inactive</span>
                @endif
            </td>
            <td class="px-3 py-2.5 text-[13px] text-[#37474F]">{{ $variant->sort_order }}</td>
            <td class="px-3 py-2.5">
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('admin.variants.items.index', $variant) }}"
                       class="inline-flex items-center gap-1.5 h-[28px] px-3 text-[12px] font-semibold bg-[#D4AF37] text-[#000000] rounded hover:brightness-110 transition"
                       title="Manage Items">
                        <i class="fa-solid fa-list text-xs"></i>
                        Items
                    </a>
                    <a href="{{ route('admin.variants.items.create', $variant) }}"
                       class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition"
                       title="Add Item">
                        <i class="fa-solid fa-plus text-xs"></i>
                    </a>
                    <a href="{{ route('admin.variants.edit', $variant) }}"
                       class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition"
                       title="Edit Variant">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </a>
                    <form action="{{ route('admin.variants.destroy', $variant) }}" method="POST" class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this variant?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center w-[28px] h-[28px] bg-[#C0392B] text-white rounded hover:bg-[#a93226] transition"
                                title="Delete Variant">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="px-4 py-10 text-center text-[13px] text-[#37474F]">
                <i class="fa-solid fa-sliders text-2xl text-[#E0E0E0] mb-3 block"></i>
                No variants found.
                <a href="{{ route('admin.variants.create') }}" class="text-[#D4AF37] hover:underline ml-1">Create your first variant</a>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
