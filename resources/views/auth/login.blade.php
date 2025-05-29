<x-guest-layout>
    <div class="auth-box h-full flex flex-col justify-center">
        <div class="mobile-logo text-center mb-6 lg:hidden flex justify-center">
            <div class="mb-10 inline-flex items-center justify-center">
                <x-application-logo :textSize="true" />
               {{--  <span
                    class="ltr:ml-3 rtl:mr-3 text-xl font-Inter font-bold text-slate-900 dark:text-white">{{ config('app.name') }}</span> --}}
            </div>
        </div>
        <div class="text-center 2xl:mb-10 mb-4">
            <h4 class="font-medium"> {{ __('Login') }}</h4>
            <div class="text-slate-500 text-base">
                {!! __('Iniciar sesi&oacute;n en ' . config('app.name')) !!}
            </div>
        </div>
        <x-login-form></x-login-form>
    </div>
</x-guest-layout>
