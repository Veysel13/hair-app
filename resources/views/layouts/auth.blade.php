<!DOCTYPE html>
<html lang="tr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Giriş') — Salonim</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:#0f0f14; min-height:100vh; display:flex; align-items:center; justify-content:center; font-family:'Inter',sans-serif;">
    <div style="width:100%; max-width:420px; padding:1.5rem;">
        @yield('content')
    </div>
</body>
</html>
