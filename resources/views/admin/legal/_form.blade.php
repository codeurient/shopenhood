@php
    $currentActions = isset($policy) && $policy ? $policy->platformActions->pluck('action')->toArray() : [];
@endphp

{{-- Basic Information --}}
<div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
    <div class="px-4 py-3 border-b border-[#E0E0E0]">
        <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
            <i class="fa-solid fa-circle-info text-[#D4AF37]"></i>
            Basic Information
        </h3>
    </div>
    <div class="p-4 space-y-4">

        <div>
            <label for="title" class="block text-[12px] font-medium text-[#37474F] mb-1">
                Policy Title <span class="text-red-500">*</span>
            </label>
            <input type="text" id="title" name="title"
                   value="{{ old('title', $policy?->title) }}"
                   class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3"
                   placeholder="e.g. Terms of Service" required>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="policy_type" class="block text-[12px] font-medium text-[#37474F] mb-1">
                    Policy Type <span class="text-red-500">*</span>
                </label>
                <select id="policy_type" name="policy_type"
                        class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3" required>
                    <option value="">Select a type…</option>
                    @foreach($policyTypes as $key => $label)
                        <option value="{{ $key }}" @selected(old('policy_type', $policy?->policy_type) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="version_field" class="block text-[12px] font-medium text-[#37474F] mb-1">
                    Version <span class="text-red-500">*</span>
                    @isset($policy)
                        <span class="text-[11px] text-[#37474F] font-normal">(use "Bump version" above to archive)</span>
                    @endisset
                </label>
                @if(!isset($policy) || !$policy)
                    <input type="text" id="version_field" name="version"
                           value="{{ old('version', '1.0') }}"
                           class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 font-mono"
                           placeholder="1.0" required>
                @else
                    <input type="text" id="version_field"
                           value="v{{ $policy->version }}"
                           class="w-full h-[34px] border border-[#E0E0E0] bg-gray-50 text-[#37474F] text-[13px] rounded px-3 font-mono cursor-not-allowed" readonly>
                    <input type="hidden" name="version" value="{{ $policy->version }}">
                @endif
            </div>
        </div>

        <div>
            <label for="status" class="block text-[12px] font-medium text-[#37474F] mb-1">
                Status <span class="text-red-500">*</span>
            </label>
            <select id="status" name="status"
                    class="w-full h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3">
                <option value="active" @selected(old('status', $policy?->status ?? 'active') === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $policy?->status) === 'inactive')>Inactive</option>
            </select>
        </div>

        <div>
            <label for="description" class="block text-[12px] font-medium text-[#37474F] mb-1">Short Description</label>
            <textarea id="description" name="description" rows="2"
                      class="w-full border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 py-2 resize-none"
                      placeholder="One-sentence summary shown in listings and footer links">{{ old('description', $policy?->description) }}</textarea>
        </div>

    </div>
</div>

{{-- Policy Content --}}
<div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
    <div class="px-4 py-3 border-b border-[#E0E0E0]">
        <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
            <i class="fa-solid fa-file-lines text-[#D4AF37]"></i>
            Policy Content <span class="text-red-500">*</span>
        </h3>
    </div>
    <div class="p-4">
        <p class="text-[12px] text-[#37474F] mb-2">HTML is supported. Use headings, paragraphs, and lists to structure the document.</p>
        <textarea id="content" name="content" rows="20"
                  class="w-full border border-[#E0E0E0] text-[#1A1A1A] text-[13px] rounded focus:ring-0 focus:border-[#D4AF37] px-3 py-2 font-mono resize-y"
                  placeholder="<h2>Section Title</h2>&#10;<p>Policy text goes here…</p>" required>{{ old('content', $policy?->content) }}</textarea>
    </div>
</div>

{{-- Display Settings --}}
<div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
    <div class="px-4 py-3 border-b border-[#E0E0E0]">
        <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
            <i class="fa-solid fa-flag text-[#D4AF37]"></i>
            Display &amp; Acceptance Settings
        </h3>
    </div>
    <div class="p-4">
        @php
            $checkboxes = [
                ['name' => 'require_acceptance',       'label' => 'Require User Acceptance',      'desc' => 'User must check a box to agree before proceeding'],
                ['name' => 'show_in_footer',           'label' => 'Show in Footer',               'desc' => 'Link appears in the public Legal Center footer section'],
                ['name' => 'show_during_registration', 'label' => 'Show During Registration',     'desc' => 'Displayed on the sign-up page'],
                ['name' => 'show_during_checkout',     'label' => 'Show During Checkout',         'desc' => 'Displayed on the checkout / order confirmation page'],
                ['name' => 'show_during_listing',      'label' => 'Show During Product Listing',  'desc' => 'Displayed when a seller creates a listing'],
            ];
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($checkboxes as $cb)
                <label class="flex items-start gap-3 p-3 border border-[#E0E0E0] rounded cursor-pointer hover:bg-gray-50 transition">
                    <input type="checkbox" name="{{ $cb['name'] }}" value="1"
                           class="mt-0.5 rounded border-[#E0E0E0] text-[#D4AF37] focus:ring-0"
                           @checked(old($cb['name'], $policy?->{$cb['name']}))>
                    <div>
                        <span class="text-[13px] font-medium text-[#1A1A1A]">{{ $cb['label'] }}</span>
                        <p class="text-[12px] text-[#37474F] mt-0.5">{{ $cb['desc'] }}</p>
                    </div>
                </label>
            @endforeach
        </div>
    </div>
</div>

{{-- Platform Action Assignments --}}
<div class="bg-white border border-[#E0E0E0] rounded-[6px] shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
    <div class="px-4 py-3 border-b border-[#E0E0E0]">
        <h3 class="text-[14px] font-semibold text-[#1A1A1A] flex items-center gap-2">
            <i class="fa-solid fa-bolt text-[#D4AF37]"></i>
            Platform Action Assignments
        </h3>
    </div>
    <div class="p-4">
        <p class="text-[13px] text-[#37474F] mb-3">
            Select which platform actions trigger this policy. When a user performs any of these actions
            and <em>require acceptance</em> is enabled, they must agree before continuing.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($platformActions as $key => $label)
                <label class="flex items-center gap-2.5 p-3 border border-[#E0E0E0] rounded cursor-pointer hover:bg-gray-50 transition">
                    <input type="checkbox" name="platform_actions[]" value="{{ $key }}"
                           class="rounded border-[#E0E0E0] text-[#D4AF37] focus:ring-0"
                           @checked(in_array($key, old('platform_actions', $currentActions)))>
                    <span class="text-[13px] text-[#1A1A1A]">{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>
