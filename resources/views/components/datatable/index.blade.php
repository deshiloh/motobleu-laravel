@props([
    'headers' => false,
    'footer' => false
])
<table class="table w-full table-auto">
    <!-- head -->
    <thead>
        {{ $headers }}
    </thead>
    <tbody>
        {{ $body }}
    </tbody>
    @if($footer)
    <tfoot>
        {{ $footer }}
    </tfoot>
    @endif
</table>
