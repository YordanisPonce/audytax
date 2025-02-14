@props(['textSize'])
<a class="flex items-center" href="{{ url('/') }}">
    <img src="{{ asset('images/logo/audytax-logo.png') }}" @class([
        'h-16' => isset($textSize),
        'h-10' => !isset($textSize),
    ]) alt="logo">
</a>
