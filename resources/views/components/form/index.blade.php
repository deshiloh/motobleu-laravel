@props([
    'route' => '#',
    'method' => 'get',
    'put' => false,
    'extra' => ''
])
<form action="{{ $route }}" method="{{ $method }}">
    @csrf
    @if($put)
        @method('PUT')
    @endif
    {{ $slot }}
    <div class="flex items-center">
        <x-form.submit></x-form.submit>
        @yield('extra')
    </div>
</form>
