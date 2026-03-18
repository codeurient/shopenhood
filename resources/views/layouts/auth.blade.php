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
            theme: {
                extend: {
                    colors: {
                        luxury: {
                            black:    '#000000',
                            surface:  '#1A1A1A',
                            charcoal: '#37474F',
                            gold:     '#D4AF37',
                            light:    '#E0E0E0',
                            white:    '#FFFFFF',
                            text:     '#1A1A1A',
                        },
                        primary: {
                            50:  '#f0f8ff',
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
                    },
                    fontFamily: {
                        sans: ['Inter', 'Segoe UI', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="bg-white antialiased font-sans flex flex-col h-screen">

    <main class="flex-1 overflow-hidden">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="flex-shrink-0 border-t border-[#E0E0E0] bg-white">
        <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-center sm:justify-end gap-2">
            <nav class="flex items-center gap-4">
                <a href="#" class="text-xs text-[#37474F] hover:text-[#D4AF37] transition-colors">Privacy Policy</a>
                <a href="#" class="text-xs text-[#37474F] hover:text-[#D4AF37] transition-colors">Terms of Service</a>
                <a href="#" class="text-xs text-[#37474F] hover:text-[#D4AF37] transition-colors">Help &amp; Support</a>
                <a href="{{ route('home') }}" class="text-xs text-[#37474F] hover:text-[#D4AF37] transition-colors">Back to Home</a>
            </nav>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
