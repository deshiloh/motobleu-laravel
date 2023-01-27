<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css"
    />
    @livewireStyles()
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased h-full">
    {{ $slot }}
<wireui:scripts/>
<script src="https://cdn.jsdelivr.net/npm/theme-change@2.0.2/index.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.10.2/dist/cdn.min.js"></script>
@livewireScripts()
@stack('scripts')
</body>
</html>

