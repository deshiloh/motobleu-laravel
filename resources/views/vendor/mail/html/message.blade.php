@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

Cordialement,<br>
{{ config('app.name') }}
{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
MotoBleu <br> 26 - 28 rue Marius Aufan, 92300 Levallois Perret <br>Tél: +33647938617, <span style="color:#FFFFFF!important;">contact@motobleu-paris.com</span>, RCS 824 721 955 NANTERRE
{{--© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')--}}
@endcomponent
@endslot
@endcomponent
