<!-- Sidebar Overlay -->
<div x-show="sidebarOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-50 bg-black bg-opacity-50"
     style="display: none;">
</div>

<!-- Sidebar -->
<aside x-show="sidebarOpen"
       x-transition:enter="transition ease-out duration-300 transform"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="fixed inset-y-0 left-0 z-50 w-80 bg-white shadow-xl overflow-y-auto"
       style="display: none;">

    <!-- Sidebar Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200">
        <a href="{{ route('home') }}" class="text-2xl font-bold text-primary-600">
            {{ config('app.name', 'Shopenhood') }}
        </a>
        <button @click="sidebarOpen = false"
                type="button"
                class="flex items-center justify-center w-10 h-10 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="p-4 space-y-1">
        <!-- Stores -->
        <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            Stores
        </a>

        <!-- Help -->
        <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            Help
        </a>

        <!-- About -->
        <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            About
        </a>

        <!-- User Agreement -->
        <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            User Agreement
        </a>

        <!-- Privacy Policy -->
        <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            Privacy Policy
        </a>

        <!-- Advertise -->
        <a href="#" class="block px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            Advertise
        </a>

        @auth
            @if(auth()->user()->isBusinessUser())
                <!-- For Business (with badge) -->
                <a href="{{ route('business.profile') }}"
                   class="flex items-center justify-between px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <span>For Business</span>
                    <span class="px-2 py-1 text-xs font-semibold text-white bg-primary-600 rounded-full">New</span>
                </a>
            @endif
        @endauth
    </nav>

    <!-- Sidebar Footer -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 bg-white">
        <!-- Language Selector -->
        <div class="flex items-center gap-2 mb-4 text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
            </svg>
            <span class="text-sm">English</span>
        </div>

        <!-- Contact Info -->
        <div class="space-y-2 text-sm text-gray-600">
            <p class="font-semibold text-gray-700">Contact Us</p>
            <a href="tel:+123456789" class="flex items-center gap-2 hover:text-primary-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                (012) 526-19-19
            </a>
            <a href="mailto:info@shopenhood.com" class="flex items-center gap-2 hover:text-primary-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                info@shopenhood.com
            </a>
        </div>

        <!-- Social Media -->
        <div class="flex gap-4 mt-4">
            <a href="#" class="text-gray-600 hover:text-primary-600 transition-colors">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </a>
            <a href="#" class="text-gray-600 hover:text-primary-600 transition-colors">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                </svg>
            </a>
        </div>
    </div>
</aside>
