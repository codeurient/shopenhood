<x-guest-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Business Profile</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage your business information
                </p>
            </div>
            <a href="{{ route('business.profile.edit') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-600 transition">
                Edit Profile
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-800 dark:text-red-200 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Branding -->
                    @if($businessProfile->banner || $businessProfile->logo)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        @if($businessProfile->banner)
                            <div class="h-48 bg-gray-100 dark:bg-gray-700">
                                <img src="{{ asset('storage/' . $businessProfile->banner) }}" alt="Banner" class="w-full h-full object-cover">
                            </div>
                        @endif
                        @if($businessProfile->logo)
                            <div class="px-6 py-4 {{ $businessProfile->banner ? '-mt-12' : '' }}">
                                <img src="{{ asset('storage/' . $businessProfile->logo) }}" alt="Logo" class="w-24 h-24 rounded-lg object-cover border-4 border-white dark:border-gray-800 shadow">
                            </div>
                        @endif
                    </div>
                    @endif

                    <!-- Basic Information -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Business Information</h3>
                        </div>
                        <div class="px-6 py-4">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Business Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $businessProfile->business_name }}</dd>
                                </div>
                                @if($businessProfile->legal_name)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Legal Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $businessProfile->legal_name }}</dd>
                                </div>
                                @endif
                                @if($businessProfile->industry)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Industry</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($businessProfile->industry) }}</dd>
                                </div>
                                @endif
                                @if($businessProfile->business_type)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Business Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ str_replace('_', ' ', ucfirst($businessProfile->business_type)) }}</dd>
                                </div>
                                @endif
                            </dl>

                            @if($businessProfile->description)
                            <div class="mt-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Description</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100 prose prose-sm dark:prose-invert max-w-none">
                                    {!! nl2br(e($businessProfile->description)) !!}
                                </dd>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Contact Information -->
                    @if($businessProfile->business_email || $businessProfile->business_phone || $businessProfile->website)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Contact Information</h3>
                        </div>
                        <div class="px-6 py-4">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                @if($businessProfile->business_email)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        <a href="mailto:{{ $businessProfile->business_email }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $businessProfile->business_email }}</a>
                                    </dd>
                                </div>
                                @endif
                                @if($businessProfile->business_phone)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $businessProfile->business_phone }}</dd>
                                </div>
                                @endif
                                @if($businessProfile->website)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Website</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        <a href="{{ $businessProfile->website }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $businessProfile->website }}</a>
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                    @endif

                    <!-- Policies -->
                    @if($businessProfile->return_policy || $businessProfile->shipping_policy)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Policies</h3>
                        </div>
                        <div class="px-6 py-4 space-y-6">
                            @if($businessProfile->return_policy)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Return Policy</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100 prose prose-sm dark:prose-invert max-w-none">
                                    {!! nl2br(e($businessProfile->return_policy)) !!}
                                </dd>
                            </div>
                            @endif
                            @if($businessProfile->shipping_policy)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Shipping Policy</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100 prose prose-sm dark:prose-invert max-w-none">
                                    {!! nl2br(e($businessProfile->shipping_policy)) !!}
                                </dd>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Address -->
                    @if($businessProfile->address_line_1 || $businessProfile->city || $businessProfile->country)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Address</h3>
                        </div>
                        <div class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            @if($businessProfile->address_line_1)
                                {{ $businessProfile->address_line_1 }}<br>
                            @endif
                            @if($businessProfile->address_line_2)
                                {{ $businessProfile->address_line_2 }}<br>
                            @endif
                            @if($businessProfile->city || $businessProfile->state_province || $businessProfile->postal_code)
                                {{ $businessProfile->city }}{{ $businessProfile->city && $businessProfile->state_province ? ', ' : '' }}{{ $businessProfile->state_province }} {{ $businessProfile->postal_code }}<br>
                            @endif
                            @if($businessProfile->country)
                                {{ $businessProfile->country->name }}
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Settings -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Settings</h3>
                        </div>
                        <div class="px-6 py-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Default Currency</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $businessProfile->default_currency }}</span>
                            </div>
                            @if($businessProfile->timezone)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Timezone</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $businessProfile->timezone }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Profile Info</h3>
                        </div>
                        <div class="px-6 py-4 space-y-3 text-sm">
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Created</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $businessProfile->created_at->format('M d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $businessProfile->updated_at->format('M d, Y H:i') }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
