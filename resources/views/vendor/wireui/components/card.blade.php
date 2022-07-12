<div class="{{ $cardClasses }}">
    @if ($header)
        {{ $header }}
    @elseif ($title || $action)
        <div class="{{ $headerClasses }}">
            <h3 class="font-medium whitespace-normal text-md text-secondary">{{ $title }}</h3>

            @if ($action)
                {{ $action }}
            @endif
        </div>
    @endif

    <div {{ $attributes->merge(['class' => "{$padding} text-secondary grow"]) }}>
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="{{ $footerClasses }}">
            {{ $footer }}
        </div>
    @endif
</div>
