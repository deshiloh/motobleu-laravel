@props([
    'route' => '#',
    'method' => 'get'
])
<form action="{{ $route }}" method="{{ $method }}">
    @csrf
    {{ $slot }}
</form>
