<x-app-layout>

    {{-- ── Page Header ─────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('user.addresses.index') }}"
               class="inline-flex items-center gap-1.5 text-[12px] text-[#37474F] hover:text-[#D4AF37] transition mb-2">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                Back to Addresses
            </a>
            <h1 class="text-xl font-bold text-[#1A1A1A]">Edit Address</h1>
        </div>
    </div>

    {{-- ── Validation Errors ────────────────────────────────────────────── --}}
    @if($errors->any())
        <div class="mb-5 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded text-[13px]">
            <i class="fa-solid fa-circle-exclamation flex-shrink-0 mt-0.5"></i>
            <ul class="space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.addresses.update', $address) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-5 max-w-3xl">

            {{-- ── Label & Default ─────────────────────────────────────── --}}
            <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-tag text-[#37474F] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Address Settings</h2>
                </div>
                <div class="px-5 py-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                            Label <span class="text-red-500">*</span>
                        </label>
                        <select name="label" required
                                class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                            <option value="Home"  {{ old('label', $address->label) === 'Home'  ? 'selected' : '' }}>Home</option>
                            <option value="Work"  {{ old('label', $address->label) === 'Work'  ? 'selected' : '' }}>Work</option>
                            <option value="Other" {{ old('label', $address->label) === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_default" value="1"
                                   {{ old('is_default', $address->is_default) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded accent-[#D4AF37]">
                            <span class="text-[13px] text-[#37474F]">Set as default address</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ── Recipient Information ────────────────────────────────── --}}
            <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-user text-[#37474F] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Recipient Information</h2>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                Recipient Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="recipient_name" required
                                   value="{{ old('recipient_name', $address->recipient_name) }}"
                                   placeholder="Full name"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                Phone <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="phone" required
                                   value="{{ old('phone', $address->phone) }}"
                                   placeholder="+1 234 567 8900"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Email</label>
                        <input type="email" name="email"
                               value="{{ old('email', $address->email) }}"
                               placeholder="Optional — for delivery notifications"
                               class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                    </div>
                </div>
            </div>

            {{-- ── Address Details ──────────────────────────────────────── --}}
            <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden"
                 x-data="{
                     cities: {{ json_encode($cities->values()) }},
                     loadCities(id) {
                         if (!id) { this.cities = []; return; }
                         fetch(`/api/locations/${id}/cities`)
                             .then(r => r.json())
                             .then(data => { this.cities = data.cities ?? []; });
                     }
                 }">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-location-dot text-[#37474F] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Address Details</h2>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                Country <span class="text-red-500">*</span>
                            </label>
                            <select name="country" required
                                    @change="loadCities($event.target.options[$event.target.selectedIndex].dataset.id)"
                                    class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                <option value="">— Select Country —</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->name }}" data-id="{{ $country->id }}"
                                            {{ old('country', $address->country) === $country->name ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                                City <span class="text-red-500">*</span>
                            </label>
                            @php $selectedCity = old('city', $address->city); @endphp
                            <select name="city" required
                                    class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] focus:border-[#D4AF37] focus:outline-none transition">
                                <option value="">— Select City —</option>
                                <template x-for="city in cities" :key="city.id">
                                    <option :value="city.name" x-text="city.name"
                                            :selected="city.name === '{{ $selectedCity }}'"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">
                            Street Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="street" required
                               value="{{ old('street', $address->street) }}"
                               placeholder="Street name and number"
                               class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Building</label>
                            <input type="text" name="building"
                                   value="{{ old('building', $address->building) }}"
                                   placeholder="Building name/no."
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Apartment</label>
                            <input type="text" name="apartment"
                                   value="{{ old('apartment', $address->apartment) }}"
                                   placeholder="Apt / unit"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">Postal Code</label>
                            <input type="text" name="postal_code"
                                   value="{{ old('postal_code', $address->postal_code) }}"
                                   placeholder="ZIP / postal"
                                   class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-[#37474F] uppercase tracking-wider mb-1.5">District</label>
                        <input type="text" name="district"
                               value="{{ old('district', $address->district) }}"
                               placeholder="District or neighborhood"
                               class="w-full h-[34px] px-3 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition">
                    </div>
                </div>
            </div>

            {{-- ── Additional Notes ─────────────────────────────────────── --}}
            <div class="bg-white border border-[#E0E0E0] rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center gap-2 px-5 py-3 border-b border-[#E0E0E0]">
                    <i class="fa-solid fa-note-sticky text-[#37474F] text-sm"></i>
                    <h2 class="text-[13px] font-semibold text-[#1A1A1A]">Additional Notes</h2>
                </div>
                <div class="px-5 py-4">
                    <textarea name="additional_notes" rows="3"
                              placeholder="Delivery instructions, landmarks, gate codes, etc."
                              class="w-full px-3 py-2 bg-white border border-[#E0E0E0] rounded text-[13px] text-[#1A1A1A] placeholder-[#37474F]/50 focus:border-[#D4AF37] focus:outline-none transition resize-none">{{ old('additional_notes', $address->additional_notes) }}</textarea>
                </div>
            </div>

            {{-- ── Actions ──────────────────────────────────────────────── --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('user.addresses.index') }}"
                   class="inline-flex items-center gap-2 h-[34px] px-4 border border-[#E0E0E0] text-[#37474F] text-[13px] font-semibold rounded hover:bg-[#F5F5F5] transition">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 h-[34px] px-5 bg-[#D4AF37] text-[#000000] text-[13px] font-semibold rounded hover:brightness-110 transition">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Update Address
                </button>
            </div>
        </div>
    </form>

</x-app-layout>
