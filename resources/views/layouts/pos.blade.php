<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>POS Kasir</title>
    @filamentStyles
    @stack('styles')
</head>
<body class="h-screen overflow-hidden bg-gray-50 dark:bg-gray-950 antialiased">
    {{ $slot }}

    @filamentScripts
    @stack('scripts')
</body>
</html>
