<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>POS Kasir</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {!! \Filament\Support\Facades\FilamentAsset::renderStyles() !!}
    @stack('styles')
    <script>
        const theme = localStorage.getItem('theme') ?? 'system';
        if (
            theme === 'dark' ||
            (theme === 'system' &&
                window.matchMedia('(prefers-color-scheme: dark)').matches)
        ) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="h-screen overflow-hidden bg-slate-50 dark:bg-slate-950 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-indigo-50/40 via-slate-50 to-slate-50 dark:from-indigo-900/20 dark:via-slate-950 dark:to-slate-950 antialiased selection:bg-primary-500/30">
    {{ $slot }}

    {!! \Filament\Support\Facades\FilamentAsset::renderScripts() !!}
    <livewire:notifications />
    @stack('scripts')
</body>
</html>
