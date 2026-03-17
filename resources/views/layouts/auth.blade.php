<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Shopenhood') }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
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
<body class="bg-gray-50 antialiased flex flex-col h-screen">

    <main class="flex-1 overflow-hidden">
        {{ $slot }}
    </main>

    <footer class="flex-shrink-0 border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 py-3 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-xs text-gray-400">
                &copy; {{ date('Y') }} <span class="font-medium text-gray-500">{{ config('app.name', 'Shopenhood') }}</span>. All rights reserved.
            </p>
            <nav class="flex items-center gap-4">
                <a href="#" class="text-xs text-gray-400 hover:text-primary-600 transition-colors">Privacy Policy</a>
                <a href="#" class="text-xs text-gray-400 hover:text-primary-600 transition-colors">Terms of Service</a>
                <a href="#" class="text-xs text-gray-400 hover:text-primary-600 transition-colors">Help &amp; Support</a>
                <a href="{{ route('home') }}" class="text-xs text-gray-400 hover:text-primary-600 transition-colors">Back to Home</a>
            </nav>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
