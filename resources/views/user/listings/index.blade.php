<x-app-layout>

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-[#1A1A1A]">My Listings</h1>
            <p class="text-xs text-[#37474F] mt-0.5">Manage your active and deleted listings</p>
        </div>
        <a href="{{ route('user.listings.create') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
            <i class="fa-solid fa-plus text-xs"></i>
            New Listing
        </a>
    </div>

    {{-- ── Flash Messages ───────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded text-[13px]">
            <i class="fa-solid fa-circle-check flex-shrink-0"></i>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded text-[13px]">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── KPI Strip (mirrors dashboard) ──────────────────────────────── --}}
    @php
        $statusMap = $activeListings->groupBy('status');
        $kpi = [
            'total'    => $activeListings->count(),
            'active'   => $statusMap->get('active',   collect())->count(),
            'pending'  => $statusMap->get('pending',  collect())->count(),
            'expired'  => $statusMap->get('expired',  collect())->count(),
            'rejected' => $statusMap->get('rejected', collect())->count(),
        ];
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
        @foreach([
            ['label' => 'Total',    'value' => $kpi['total'],    'icon' => 'fa-list',              'color' => 'text-[#D4AF37]'],
            ['label' => 'Active',   'value' => $kpi['active'],   'icon' => 'fa-circle-check',      'color' => 'text-green-500'],
            ['label' => 'Pending',  'value' => $kpi['pending'],  'icon' => 'fa-clock',             'color' => 'text-yellow-500'],
            ['label' => 'Expired',  'value' => $kpi['expired'],  'icon' => 'fa-calendar-xmark',   'color' => 'text-red-400'],
            ['label' => 'Rejected', 'value' => $kpi['rejected'], 'icon' => 'fa-ban',              'color' => 'text-red-500'],
        ] as $tile)
        <div class="bg-white border border-[#E0E0E0] rounded-lg px-4 py-3 flex items-center gap-3 shadow-sm">
            <div class="flex-shrink-0 w-8 h-8 rounded bg-[#F5F5F5] flex items-center justify-center">
                <i class="fa-solid {{ $tile['icon'] }} text-sm {{ $tile['color'] }}"></i>
            </div>
            <div>
                <p class="text-[11px] text-[#37474F] leading-none mb-0.5">{{ $tile['label'] }}</p>
                <p class="text-lg font-bold text-[#1A1A1A] leading-none">{{ $tile['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Active Listings Table ────────────────────────────────────────── --}}
    <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden mb-6"
         x-data="{ selectedActiveIds: [] }">

        {{-- Table Header --}}
        <div class="flex items-center justify-between gap-4 px-5 py-3.5 border-b border-[#E0E0E0]">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-rectangle-list text-[#37474F] text-sm"></i>
                <h2 class="text-[13px] font-semibold text-[#1A1A1A]">
                    Active Listings
                    <span class="ml-1 px-2 py-0.5 text-[11px] font-semibold bg-[#D4AF37]/15 text-[#1A1A1A] rounded-full">{{ $activeListings->count() }}</span>
                </h2>
            </div>
            <div x-show="selectedActiveIds.length > 0" style="display:none;">
                <form action="{{ route('user.listings.bulk-destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <template x-for="id in selectedActiveIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="button"
                        @click="$dispatch('open-confirm-modal', { message: 'Delete selected listings?', form: $el.closest('form') })"
                        class="inline-flex items-center gap-1.5 h-[28px] px-3 bg-red-600 text-white text-[12px] font-semibold rounded hover:bg-red-700 transition">
                        <i class="fa-solid fa-trash text-[10px]"></i>
                        Delete Selected (<span x-text="selectedActiveIds.length"></span>)
                    </button>
                </form>
            </div>
        </div>

        @if($activeListings->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-[#37474F]">
                        <th class="px-4 py-2.5 w-10">
                            <input type="checkbox"
                                   @change="selectedActiveIds = $event.target.checked ? [{{ $activeListings->pluck('id')->join(',') }}] : []"
                                   class="rounded border-[#E0E0E0] w-3.5 h-3.5 accent-[#D4AF37]">
                        </th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider">Listing</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider hidden md:table-cell">Category</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider hidden sm:table-cell">Price</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2.5 text-right text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E0E0]">
                    @foreach($activeListings as $i => $listing)
                    <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-[#FAFAFA]' }} hover:bg-[#D4AF37]/5 transition-colors">

                        {{-- Checkbox --}}
                        <td class="px-4 py-3">
                            <input type="checkbox" :value="{{ $listing->id }}" x-model="selectedActiveIds"
                                   class="rounded border-[#E0E0E0] w-3.5 h-3.5 accent-[#D4AF37]">
                        </td>

                        {{-- Listing --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($listing->primaryImage)
                                    <img src="{{ asset('storage/' . $listing->primaryImage->image_path) }}"
                                         alt="{{ $listing->title }}"
                                         class="w-10 h-10 rounded object-cover flex-shrink-0 border border-[#E0E0E0]">
                                @else
                                    <div class="w-10 h-10 rounded bg-[#F5F5F5] border border-[#E0E0E0] flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-image text-[#37474F] text-sm"></i>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ route('user.listings.show', $listing) }}"
                                       class="text-[13px] font-medium text-[#1A1A1A] hover:text-[#D4AF37] transition-colors truncate block max-w-[180px]">
                                        {{ $listing->title }}
                                    </a>
                                    @if($listing->listingType)
                                        <span class="text-[11px] text-[#37474F]">{{ $listing->listingType->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Category --}}
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="text-[13px] text-[#37474F]">{{ $listing->category->name ?? '—' }}</span>
                        </td>

                        {{-- Price --}}
                        <td class="px-4 py-3 hidden sm:table-cell">
                            @if($listing->base_price)
                                <span class="text-[13px] font-semibold text-[#1A1A1A]">
                                    {{ $listing->currency ?? 'USD' }} {{ number_format($listing->base_price, 2) }}
                                </span>
                            @else
                                <span class="text-[13px] text-[#37474F]">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            @php
                                $badge = match($listing->status) {
                                    'active'   => 'bg-green-100 text-green-700',
                                    'pending'  => 'bg-yellow-100 text-yellow-700',
                                    'draft'    => 'bg-[#E0E0E0] text-[#37474F]',
                                    'expired'  => 'bg-red-100 text-red-600',
                                    'rejected' => 'bg-red-100 text-red-600',
                                    'sold'     => 'bg-blue-100 text-blue-700',
                                    default    => 'bg-[#E0E0E0] text-[#37474F]',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $badge }}">
                                {{ ucfirst($listing->status) }}
                            </span>
                            @if($listing->status === 'rejected' && $listing->rejection_reason)
                                <p class="text-[11px] text-red-500 mt-0.5 max-w-[160px] truncate"
                                   title="{{ $listing->rejection_reason }}">{{ $listing->rejection_reason }}</p>
                            @endif
                            @if($listing->hidden_due_to_role_change)
                                <span class="mt-0.5 inline-block px-2 py-0.5 text-[11px] font-semibold rounded-full bg-orange-100 text-orange-700">
                                    Role-hidden
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('user.listings.edit', $listing) }}"
                                   class="inline-flex items-center gap-1 h-[28px] px-2.5 bg-[#D4AF37] text-[#000000] text-[12px] font-semibold rounded hover:brightness-110 transition"
                                   title="Edit">
                                    <i class="fa-solid fa-pen text-[10px]"></i>
                                    <span class="hidden sm:inline">Edit</span>
                                </a>

                                <form action="{{ route('user.listings.toggle', $listing) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 h-[28px] px-2.5 border border-[#E0E0E0] text-[#37474F] text-[12px] font-semibold rounded hover:bg-[#F5F5F5] transition"
                                            title="{{ $listing->is_visible ? 'Hide' : 'Show' }}">
                                        <i class="fa-solid {{ $listing->is_visible ? 'fa-eye-slash' : 'fa-eye' }} text-[10px]"></i>
                                        <span class="hidden sm:inline">{{ $listing->is_visible ? 'Hide' : 'Show' }}</span>
                                    </button>
                                </form>

                                <form action="{{ route('user.listings.destroy', $listing) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        @click="$dispatch('open-confirm-modal', { message: 'Delete this listing?', form: $el.closest('form') })"
                                        class="inline-flex items-center gap-1 h-[28px] px-2.5 border border-red-200 text-red-600 text-[12px] font-semibold rounded hover:bg-red-50 transition"
                                        title="Delete">
                                        <i class="fa-solid fa-trash text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-14 text-center">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-[#F5F5F5] flex items-center justify-center">
                <i class="fa-solid fa-rectangle-list text-[#37474F] text-xl"></i>
            </div>
            <p class="text-[14px] font-semibold text-[#1A1A1A] mb-1">No listings yet</p>
            <p class="text-[13px] text-[#37474F] mb-4">Create your first listing to start selling.</p>
            <a href="{{ route('user.listings.create') }}"
               class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                <i class="fa-solid fa-plus text-xs"></i>
                Create Listing
            </a>
        </div>
        @endif
    </div>

    {{-- ── Deleted Listings ─────────────────────────────────────────────── --}}
    @if($trashedListings->count() > 0)
    <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden"
         x-data="{ selectedTrashedIds: [] }">

        <div class="flex items-center justify-between gap-4 px-5 py-3.5 border-b border-[#E0E0E0] flex-wrap">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-trash-can text-red-400 text-sm"></i>
                <div>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">
                        Deleted Listings
                        <span class="ml-1 px-2 py-0.5 text-[11px] font-semibold bg-red-100 text-red-600 rounded-full">{{ $trashedListings->count() }}</span>
                    </h2>
                    <p class="text-[11px] text-[#37474F] mt-0.5">
                        Kept for {{ $retentionDays }} days before permanent removal.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <form action="{{ route('user.listings.bulk-force-destroy-trashed') }}" method="POST"
                      x-show="selectedTrashedIds.length > 0" style="display:none;">
                    @csrf
                    <template x-for="id in selectedTrashedIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="button"
                        @click="$dispatch('open-confirm-modal', { message: 'Permanently delete selected? This cannot be undone.', form: $el.closest('form') })"
                        class="inline-flex items-center gap-1.5 h-[28px] px-3 border border-red-300 text-red-600 text-[12px] font-semibold rounded hover:bg-red-50 transition">
                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                        Delete Selected
                    </button>
                </form>

                <form action="{{ route('user.listings.force-destroy-all-trashed') }}" method="POST">
                    @csrf
                    <button type="button"
                        @click="$dispatch('open-confirm-modal', { message: 'Permanently delete ALL deleted listings? This cannot be undone.', form: $el.closest('form') })"
                        class="inline-flex items-center gap-1.5 h-[28px] px-3 bg-red-600 text-white text-[12px] font-semibold rounded hover:bg-red-700 transition">
                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                        Delete All
                    </button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-[#37474F]">
                        <th class="px-4 py-2.5 w-10">
                            <input type="checkbox"
                                   @change="selectedTrashedIds = $event.target.checked ? [{{ $trashedListings->pluck('id')->join(',') }}] : []"
                                   class="rounded border-[#E0E0E0] w-3.5 h-3.5 accent-[#D4AF37]">
                        </th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider">Listing</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider hidden sm:table-cell">Deleted</th>
                        <th class="px-4 py-2.5 text-right text-[11px] font-semibold text-[#D4AF37] uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E0E0E0]">
                    @foreach($trashedListings as $i => $listing)
                    <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-[#FAFAFA]' }} opacity-80 hover:opacity-100 transition-opacity">
                        <td class="px-4 py-3">
                            <input type="checkbox" :value="{{ $listing->id }}" x-model="selectedTrashedIds"
                                   class="rounded border-[#E0E0E0] w-3.5 h-3.5 accent-[#D4AF37]">
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded bg-[#F5F5F5] border border-[#E0E0E0] flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-box-archive text-[#37474F] text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[13px] font-medium text-[#1A1A1A] line-through">{{ $listing->title }}</p>
                                    <p class="text-[11px] text-[#37474F]">{{ $listing->category->name ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <span class="text-[13px] text-[#37474F]">{{ $listing->deleted_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <form action="{{ route('user.listings.reshare', $listing->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 h-[28px] px-2.5 bg-green-600 text-white text-[12px] font-semibold rounded hover:bg-green-700 transition">
                                        <i class="fa-solid fa-rotate-right text-[10px]"></i>
                                        <span class="hidden sm:inline">Reshare</span>
                                    </button>
                                </form>
                                <form action="{{ route('user.listings.force-destroy', $listing->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        @click="$dispatch('open-confirm-modal', { message: 'Permanently delete this listing? This cannot be undone.', form: $el.closest('form') })"
                                        class="inline-flex items-center gap-1 h-[28px] px-2.5 border border-red-200 text-red-600 text-[12px] font-semibold rounded hover:bg-red-50 transition">
                                        <i class="fa-solid fa-trash text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</x-app-layout>
