@props([
    'active' => false
])
<!-- Active: "bg-gray-100", Not Active: "" -->
<a {{ $attributes->class([
    'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100',
    'bg-gray-100' => $active,
]) }} role="menuitem" tabindex="-1" id="user-menu-item-0">
    {{ $slot }}
</a>
