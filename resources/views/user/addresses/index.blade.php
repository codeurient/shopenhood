<x-app-layout>

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-[#1A1A1A]">My Addresses</h1>
            <p class="text-xs text-[#37474F] mt-0.5">Manage your saved delivery addresses</p>
        </div>
        <a href="{{ route('user.addresses.create') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
            <i class="fa-solid fa-plus text-xs"></i>
            Add Address
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

    {{-- ── Address Cards ────────────────────────────────────────────────── --}}
    @if($addresses->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($addresses as $address)
        <div class="bg-white border {{ $address->is_default ? 'border-[#D4AF37]' : 'border-[#E0E0E0]' }} rounded-lg shadow-sm p-5 relative flex flex-col">

            {{-- Default Badge --}}
            @if($address->is_default)
            <span class="absolute top-4 right-4 px-2 py-0.5 text-[10px] font-bold bg-[#D4AF37]/15 text-[#1A1A1A] border border-[#D4AF37]/40 rounded-full">
                Default
            </span>
            @endif

            {{-- Label --}}
            <div class="flex items-center gap-2 mb-3">
                @php
                    $labelIcon = match($address->label) {
                        'Home'  => 'fa-house',
                        'Work'  => 'fa-briefcase',
                        default => 'fa-location-dot',
                    };
                @endphp
                <div class="w-7 h-7 rounded bg-[#F5F5F5] border border-[#E0E0E0] flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid {{ $labelIcon }} text-[#37474F] text-xs"></i>
                </div>
                <span class="text-[12px] font-semibold text-[#37474F] uppercase tracking-wider">{{ $address->label }}</span>
            </div>

            {{-- Recipient --}}
            <h3 class="text-[14px] font-semibold text-[#1A1A1A] mb-0.5">{{ $address->recipient_name }}</h3>
            <p class="text-[12px] text-[#37474F] mb-3">
                {{ $address->phone }}
                @if($address->email)<br>{{ $address->email }}@endif
            </p>

            {{-- Address --}}
            <div class="text-[13px] text-[#1A1A1A] space-y-0.5 mb-4 flex-1">
                <p>{{ $address->street }}</p>
                @if($address->building || $address->apartment)
                <p>
                    @if($address->building)Bldg: {{ $address->building }}@endif
                    @if($address->building && $address->apartment), @endif
                    @if($address->apartment)Apt: {{ $address->apartment }}@endif
                </p>
                @endif
                @if($address->district)<p>{{ $address->district }}</p>@endif
                <p>{{ $address->city }}, {{ $address->country }}</p>
                @if($address->postal_code)<p class="text-[12px] text-[#37474F]">{{ $address->postal_code }}</p>@endif
            </div>

            @if($address->additional_notes)
            <p class="text-[11px] text-[#37474F] italic mb-3 border-t border-[#E0E0E0] pt-3">
                {{ Str::limit($address->additional_notes, 100) }}
            </p>
            @endif

            {{-- Actions --}}
            <div class="flex items-center gap-2 pt-3 border-t border-[#E0E0E0]">
                <a href="{{ route('user.addresses.edit', $address) }}"
                   class="inline-flex items-center gap-1.5 h-[28px] px-3 bg-[#D4AF37] text-[#000000] text-[12px] font-semibold rounded hover:brightness-110 transition">
                    <i class="fa-solid fa-pen text-[10px]"></i>
                    Edit
                </a>

                @if(!$address->is_default)
                <form action="{{ route('user.addresses.set-default', $address) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 h-[28px] px-3 border border-[#E0E0E0] text-[#37474F] text-[12px] font-semibold rounded hover:bg-[#F5F5F5] transition">
                        <i class="fa-solid fa-star text-[10px]"></i>
                        Set Default
                    </button>
                </form>
                @endif

                <form action="{{ route('user.addresses.destroy', $address) }}" method="POST" class="inline ml-auto"
                      onsubmit="return confirm('Delete this address?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1 h-[28px] px-2.5 border border-red-200 text-red-600 text-[12px] font-semibold rounded hover:bg-red-50 transition">
                        <i class="fa-solid fa-trash text-[10px]"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm py-16 text-center">
        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-[#F5F5F5] flex items-center justify-center">
            <i class="fa-solid fa-location-dot text-[#37474F] text-xl"></i>
        </div>
        <p class="text-[14px] font-semibold text-[#1A1A1A] mb-1">No addresses yet</p>
        <p class="text-[13px] text-[#37474F] mb-4">Add a delivery address to use during checkout.</p>
        <a href="{{ route('user.addresses.create') }}"
           class="inline-flex items-center gap-2 h-[34px] px-4 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
            <i class="fa-solid fa-plus text-xs"></i>
            Add Address
        </a>
    </div>
    @endif

</x-app-layout>
