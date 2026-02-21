<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <!-- Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">Upgrade to Business Account</h1>
            <p class="text-gray-500 mb-8">Unlock powerful tools to grow your business on {{ config('app.name') }}.</p>

            <!-- Features -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-left mb-8">
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800 text-sm">Multiple Listings</p>
                        <p class="text-xs text-gray-500 mt-0.5">Publish as many products as your plan allows.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800 text-sm">Product Variants</p>
                        <p class="text-xs text-gray-500 mt-0.5">Manage SKUs with different sizes, colours, and prices.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800 text-sm">Wholesale Pricing</p>
                        <p class="text-xs text-gray-500 mt-0.5">Set minimum order quantities and bulk price tiers.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-lg">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div>
                        <p class="font-medium text-gray-800 text-sm">SEO Tools & Coupons</p>
                        <p class="text-xs text-gray-500 mt-0.5">Custom meta titles, descriptions, and discount coupons.</p>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <p class="text-blue-800 font-medium mb-1">Ready to upgrade?</p>
                <p class="text-blue-600 text-sm mb-4">
                    Business accounts are available on a paid plan. Contact our admin team to get started.
                </p>
                <a href="mailto:{{ config('mail.from.address', 'admin@'.parse_url(config('app.url'), PHP_URL_HOST)) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Contact Admin
                </a>
            </div>

            <div class="mt-6">
                <a href="{{ route('user.listings.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                    &larr; Back to My Listings
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
