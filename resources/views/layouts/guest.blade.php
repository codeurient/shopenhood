<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Shopenhood') }}</title>

    <!-- SEO Meta Tags -->
    @if(isset($metaDescription))
    <meta name="description" content="{{ $metaDescription }}">
    @endif

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                    },
                },
            },
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="bg-gray-50 antialiased">
    <!-- Mobile-First Layout -->
    <div x-data="{ sidebarOpen: false }"
         x-on:keydown.escape.window="sidebarOpen = false"
         class="min-h-screen">

        <!-- Sidebar Component -->
        <x-mobile.sidebar />

        <!-- Main Content Area -->
        <div class="flex flex-col min-h-screen pb-16 md:pb-0">
            <!-- Header Component -->
            <x-mobile.header />

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- Bottom Navigation (Mobile Only) -->
            <x-mobile.bottom-nav />
        </div>
    </div>

    @stack('scripts')
</body>
</html>
