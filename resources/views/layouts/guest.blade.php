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
                            50: '#f0f8ff',
                            100: '#BDDDFC',
                            200: '#a8d3fb',
                            300: '#88BDF2',
                            400: '#6db1ef',
                            500: '#88BDF2',
                            600: '#5a9dd9',
                            700: '#4682b4',
                            800: '#2e5f7d',
                            900: '#1e4159',
                        },
                        success: {
                            50: '#f0fdf4',
                            100: '#CFFFDC',
                            500: '#68BA7F',
                            600: '#2E6F40',
                        },
                        danger: {
                            500: '#CD1C18',
                            600: '#a81614',
                        },
                        warning: {
                            500: '#C05800',
                            600: '#9a4600',
                        },
                    },
                },
            },
        }
    </script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"/>

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
