@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
JADCO
@endcomponent
@endslot

{{-- Body --}}
<div class="background-image" style="position: absolute; top: 0; right: 0; width: 200px; height: 100%; background-image: url('https://jadco.co/images/logo.png'); background-repeat: no-repeat; background-position: right top; background-size: 150px; opacity: 0.05; z-index: 0;"></div>
{{ $slot }}

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
© {{ date('Y') }} JADCO. All rights reserved.
@endcomponent
@endslot
@endcomponent
