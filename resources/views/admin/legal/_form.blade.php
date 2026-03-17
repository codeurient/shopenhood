@php
    $currentActions = isset($policy) && $policy ? $policy->platformActions->pluck('action')->toArray() : [];
@endphp

{{-- Basic Info --}}
<div class="bg-white rounded-lg shadow p-6 space-y-5">
    <h3 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-3">Basic Information</h3>

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Policy Title <span class="text-red-500">*</span></label>
        <input type="text" id="title" name="title"
               value="{{ old('title', $policy?->title) }}"
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
               placeholder="e.g. Terms of Service" required>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label for="policy_type" class="block text-sm font-medium text-gray-700 mb-1">Policy Type <span class="text-red-500">*</span></label>
            <select id="policy_type" name="policy_type"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-400" required>
                <option value="">Select a type…</option>
                @foreach($policyTypes as $key => $label)
                    <option value="{{ $key }}" @selected(old('policy_type', $policy?->policy_type) === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="version_field" class="block text-sm font-medium text-gray-700 mb-1">
                Version <span class="text-red-500">*</span>
                @isset($policy)
                    <span class="text-xs text-gray-400 font-normal">(use "Bump version" above to archive)</span>
                @endisset
            </label>
            {{-- On create we show this; on edit it's controlled by bump_version toggle --}}
            @if(!isset($policy) || !$policy)
                <input type="text" id="version_field" name="version"
                       value="{{ old('version', '1.0') }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-400"
                       placeholder="1.0" required>
            @else
                <input type="text" id="version_field"
                       value="v{{ $policy->version }}"
                       class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-500 cursor-not-allowed" readonly>
                {{-- Hidden so it's always submitted (overridden by bump_version section if checked) --}}
                <input type="hidden" name="version" value="{{ $policy->version }}">
            @endif
        </div>
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
        <select id="status" name="status"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-400">
            <option value="active" @selected(old('status', $policy?->status ?? 'active') === 'active')>Active</option>
            <option value="inactive" @selected(old('status', $policy?->status) === 'inactive')>Inactive</option>
        </select>
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
        <textarea id="description" name="description" rows="2"
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 resize-none"
                  placeholder="One-sentence summary shown in listings and footer links">{{ old('description', $policy?->description) }}</textarea>
    </div>
</div>

{{-- Full Content --}}
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-3 mb-4">Policy Content <span class="text-red-500">*</span></h3>
    <p class="text-xs text-gray-400 mb-3">HTML is supported. Use headings, paragraphs, and lists to structure the document.</p>
    <textarea id="content" name="content" rows="20"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary-400 resize-y"
              placeholder="<h2>Section Title</h2>&#10;<p>Policy text goes here…</p>" required>{{ old('content', $policy?->content) }}</textarea>
</div>

{{-- Display Settings --}}
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-3 mb-4">Display &amp; Acceptance Settings</h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @php
            $checkboxes = [
                ['name' => 'require_acceptance', 'label' => 'Require User Acceptance', 'desc' => 'User must check a box to agree before proceeding', 'color' => 'amber'],
                ['name' => 'show_in_footer', 'label' => 'Show in Footer', 'desc' => 'Link appears in the public Legal Center footer section', 'color' => 'blue'],
                ['name' => 'show_during_registration', 'label' => 'Show During Registration', 'desc' => 'Displayed on the sign-up page', 'color' => 'purple'],
                ['name' => 'show_during_checkout', 'label' => 'Show During Checkout', 'desc' => 'Displayed on the checkout / order confirmation page', 'color' => 'teal'],
                ['name' => 'show_during_listing', 'label' => 'Show During Product Listing', 'desc' => 'Displayed when a seller creates a listing', 'color' => 'orange'],
            ];
        @endphp
        @foreach($checkboxes as $cb)
        <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
            <input type="checkbox" name="{{ $cb['name'] }}" value="1" class="mt-0.5 rounded border-gray-300"
                   @checked(old($cb['name'], $policy?->{$cb['name']}))>
            <div>
                <span class="text-sm font-medium text-gray-800">{{ $cb['label'] }}</span>
                <p class="text-xs text-gray-500 mt-0.5">{{ $cb['desc'] }}</p>
            </div>
        </label>
        @endforeach
    </div>
</div>

{{-- Platform Actions --}}
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-base font-semibold text-gray-800 border-b border-gray-100 pb-3 mb-4">Platform Action Assignments</h3>
    <p class="text-sm text-gray-500 mb-4">
        Select which platform actions trigger this policy. When a user performs any of these actions
        and <em>require acceptance</em> is enabled, they must agree to this policy before continuing.
    </p>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
        @foreach($platformActions as $key => $label)
        <label class="flex items-center gap-2.5 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
            <input type="checkbox" name="platform_actions[]" value="{{ $key }}"
                   class="rounded border-gray-300"
                   @checked(in_array($key, old('platform_actions', $currentActions)))>
            <span class="text-sm text-gray-700">{{ $label }}</span>
        </label>
        @endforeach
    </div>
</div>
