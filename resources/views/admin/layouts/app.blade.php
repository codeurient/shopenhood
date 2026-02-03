<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>
    
    <!-- Tailwind CSS + Flowbite -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex-shrink-0">
            <div class="p-6 border-b border-gray-700"  style="padding:1.489rem;>
                <h2 class="text-xl font-bold">Admin Panel</h2>
            </div>
            <nav class="p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">
                    📊 Dashboard
                </a>
                <a href="{{ route('admin.categories.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700' : '' }}">
                    📁 Categories
                </a>
                <a href="{{ route('admin.variants.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.variants.*') ? 'bg-gray-700' : '' }}">
                    🔧 Variants
                </a>
                <a href="{{ route('admin.listings.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.listings.*') ? 'bg-gray-700' : '' }}">
                    🔧 Listings
                </a>
                <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-700' : '' }}">
                    👥 Users
                </a>
                <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.settings.*') ? 'bg-gray-700' : '' }}">
                    ⚙️ Settings
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                <div class="flex items-center gap-4">
                    <span class="text-gray-600">{{ auth()->guard('admin')->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Logout
                        </button>
                    </form>
                </div>
            </header>
            
            <!-- Content -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
    @stack('scripts')
</body>
</html>