<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Shopenhood') }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
        <div class="mb-8">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-800 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                {{ config('app.name', 'Shopenhood') }}
            </a>
        </div>

        <div class="w-full sm:max-w-md bg-white dark:bg-gray-800 shadow-md rounded-lg px-8 py-6">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
