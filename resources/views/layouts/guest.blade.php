<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Panel de control - Audytax</title>
        <x-favicon/>
        {{-- Scripts --}}
        @vite(['resources/css/app.scss', 'resources/js/custom/store.js'])
    </head>
    <body>

        <div class="loginwrapper">
            <div class="lg-inner-column">
                <div class="left-column relative z-[1]">
                    <div class="h-fit absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full px-20">
                        <!-- APPLICATION LOGO -->
                        <div class="mb-6">
                            <x-application-logo :textSize="true" />
                        </div>
                        <p class="text-2xl italic dark:text-white">
                            {{ __('Los controles de calidad y las auditorías mantienen altos estándares en nuestros productos y servicios.') }}
                        </p>
                    </div>
                </div>
                <div class="right-column  relative">
                    <div class="inner-content h-full flex flex-col bg-white dark:bg-slate-800">
                        {{ $slot }}
                        <div class="auth-footer text-center">
                            {{ __('Copyright') }}
                            <script>
                                document.write(new Date().getFullYear())
                            </script>
                            , <a href="#">{{ config('app.name') }}</a>
                            {{ __('Todos los derechos reservados.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @vite(['resources/js/app.js'])
    </body>
</html>
