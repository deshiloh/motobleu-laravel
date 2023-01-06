<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @livewireStyles()
</head>
<body class="antialiased h-full">
    {{ $slot }}
<wireui:scripts/>
<script src="https://cdn.jsdelivr.net/npm/theme-change@2.0.2/index.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.10.2/dist/cdn.min.js"></script>
<script src="{{ asset('js/app.js') }}" defer></script>
@livewireScripts()
@stack('scripts')
</body>
</html>

