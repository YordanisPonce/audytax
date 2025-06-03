@props(['textSize'])
<a class="flex items-center h-15" href="{{ url('/') }}">
    <img src="{{ asset('images/logo/au - logo.png') }}" @class([
        'h-16' => isset($textSize),
        'h-10' => !isset($textSize),
    ]) alt="logo">
    <span @isset($textSize)
        style="font-size: 50px;"
    @endisset>
        {{-- {{ config('app.name') }} --}}
    </span>
</a>
