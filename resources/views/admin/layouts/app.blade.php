<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    @stack('styles')
</head>
<body>

    <header>
        <h1>Admin Panel</h1>
    </header>

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
