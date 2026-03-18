<table class="w-full text-left border-collapse">
    <thead>
        <tr class="bg-[#37474F] border-b border-[#000000]/20">
            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider w-12">#</th>
            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Value</th>
            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Display Value</th>
            @if(in_array($variant->type, ['select', 'radio', 'checkbox']))
                <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Preview</th>
            @endif
            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Sort</th>
            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
            <th class="px-3 py-2.5 text-[12px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $item)
        <tr class="{{ $loop->even ? 'bg-white' : 'bg-gray-50' }} hover:bg-[#D4AF37]/5 transition-colors">
            <td class="px-3 py-2.5 text-[13px] text-[#37474F]">
                {{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}
            </td>
            <td class="px-3 py-2.5 text-[13px] font-medium text-[#1A1A1A]">{{ $item->value }}</td>
            <td class="px-3 py-2.5 text-[13px] text-[#37474F]">{{ $item->display_value ?? '—' }}</td>
            @if(in_array($variant->type, ['select', 'radio', 'checkbox']))
                <td class="px-3 py-2.5">
                    <div class="flex items-center gap-2">
                        @if($item->color_code)
                            <span class="w-7 h-7 rounded border border-[#E0E0E0] inline-block flex-shrink-0"
                                  style="background-color: {{ $item->color_code }};"
                                  title="{{ $item->color_code }}"></span>
                        @endif
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->value }}"
                                 class="w-8 h-8 object-cover rounded border border-[#E0E0E0]">
                        @endif
                        @if(!$item->color_code && !$item->image)
                            <span class="text-[#37474F]">—</span>
                        @endif
                    </div>
                </td>
            @endif
            <td class="px-3 py-2.5 text-[13px] text-[#37474F]">{{ $item->sort_order }}</td>
            <td class="px-3 py-2.5">
                @if($item->is_active)
                    <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#D4AF37]/20 text-[#D4AF37]">Active</span>
                @else
                    <span class="inline-flex items-center h-5 px-2 rounded-[10px] text-[11px] font-semibold bg-[#C0392B]/10 text-[#C0392B]">Inactive</span>
                @endif
            </td>
            <td class="px-3 py-2.5">
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('admin.variants.items.edit', [$variant, $item]) }}"
                       class="inline-flex items-center justify-center w-[28px] h-[28px] border border-[#E0E0E0] text-[#37474F] rounded hover:bg-gray-100 transition"
                       title="Edit">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </a>
                    <form action="{{ route('admin.variants.items.destroy', [$variant, $item]) }}" method="POST"
                          class="inline" onsubmit="return confirm('Are you sure you want to delete this item?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center w-[28px] h-[28px] bg-[#C0392B] text-white rounded hover:bg-[#a93226] transition"
                                title="Delete">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="{{ in_array($variant->type, ['select', 'radio', 'checkbox']) ? '7' : '6' }}"
                class="px-4 py-10 text-center text-[13px] text-[#37474F]">
                <i class="fa-solid fa-box-open text-2xl text-[#E0E0E0] mb-3 block"></i>
                No items added yet.
                <a href="{{ route('admin.variants.items.create', $variant) }}" class="text-[#D4AF37] hover:underline ml-1">Add your first item</a>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
