<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="antialiased">
    <div class="bg-blue-500 text-white p-5 flex">
        <div class="title mr-auto">
            <a href="/">Bienvenue sur motobleu laravel</a>
        </div>
        <x-menu>
            @auth()
                <x-menu.item content="Logout" link="{{ route('logout') }}"></x-menu.item>
            @endauth
        </x-menu>
    </div>
    {{ $slot }}
</body>
</html>
