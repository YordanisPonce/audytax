{{-- resources/views/components/dashboard-header.blade.php --}}
@props(['links']) {{-- Recibe la colección de QualityControls (u otra) --}}

<div class="z-[9] sticky top-0" id="app_header">
    <div class="app-header z-[999] bg-white dark:bg-slate-800 shadow-sm dark:shadow-slate-700 !ml-0">
        <div class="flex justify-between items-center h-full">

            {{-- IZQUIERDA: logo y botón hamburguesa en móvil --}}
            <div class="flex items-center md:space-x-4 space-x-4 rtl:space-x-reverse vertical-box">
                <div class="xl:hidden inline-block">
                    <x-application-logo class="mobile-logo" />
                </div>
                <button class="smallDeviceMenuController open-sdiebar-controller hidden xl:hidden md:inline-block">
                    <iconify-icon 
                        class="logo-segment leading-none bg-transparent relative text-xl top-[2px] text-slate-900 dark:text-white" 
                        icon="heroicons-outline:menu-alt-3">
                    </iconify-icon>
                </button>
            </div>
            <!-- end vertical -->

          {{-- ... (arriba permanece igual) --}}
{{-- CENTRO: Solo en XL+ mostramos los “botones” del menú --}}
<div class="hidden xl:flex flex-1 justify-start">
    <!-- Application Logo -->
        <x-application-logo />
    <nav class="flex space-x-4 overflow-x-auto px-4">
        {{-- Botón Inicio con mismo estilo que el resto --}}
        <a
            href="{{ route('dashboard.index') }}"
            class="flex items-center space-x-1 px-3 py-2 rounded 
                   hover:bg-gray-100 dark:hover:bg-slate-700 
                   {{ request()->is('dashboard*') ? 'bg-gray-200 dark:bg-slate-600 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
            <iconify-icon icon="heroicons-outline:home" class="text-lg"></iconify-icon>
            <span>{{ __('Inicio') }}</span>
        </a>

        @hasrole('admin')
            @can('user index')
                <a
                    href="{{ route('users.index') }}"
                    class="flex items-center space-x-1 px-3 py-2 rounded 
                           hover:bg-gray-100 dark:hover:bg-slate-700 
                           {{ request()->is('users*') ? 'bg-gray-200 dark:bg-slate-600 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
                    <iconify-icon icon="mdi:users" class="text-lg"></iconify-icon>
                    <span>{{ __('Usuarios') }}</span>
                </a>
            @endcan

            @can('auditoryType index')
                <a
                    href="{{ route('auditoryTypes.index') }}"
                    class="flex items-center space-x-1 px-3 py-2 rounded 
                           hover:bg-gray-100 dark:hover:bg-slate-700 
                           {{ request()->is('auditoryTypes*') || request()->is('fases*') ? 'bg-gray-200 dark:bg-slate-600 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
                    <iconify-icon icon="fluent-mdl2:compliance-audit" class="text-lg"></iconify-icon>
                    <span>{{ __('Auditory Type') }}</span>
                </a>
            @endcan

            @can('qualityControl index')
                <a
                    href="{{ route('qualityControls.index') }}"
                    class="flex items-center space-x-1 px-3 py-2 rounded 
                           hover:bg-gray-100 dark:hover:bg-slate-700 
                           {{ request()->is('qualityControls*') ? 'bg-gray-200 dark:bg-slate-600 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
                    <iconify-icon icon="icon-park-twotone:inspection" class="text-lg"></iconify-icon>
                    <span>{{ __('Quality Controls') }}</span>
                </a>
            @endcan
        @else
            @foreach($links as $link)
                @php
                    $isActive = request()->is("qualityControls/{$link->id}*");
                @endphp

                <div class="relative group">
                    <a
                        href="{{ route('qualityControls.show', $link) }}"
                        class="flex items-center space-x-1 px-3 py-2 rounded 
                               hover:bg-gray-100 dark:hover:bg-slate-700 
                               {{ $isActive ? 'bg-gray-200 dark:bg-slate-600 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
                        <iconify-icon icon="icon-park-twotone:inspection" class="text-lg"></iconify-icon>
                        <span class="truncate max-w-[100px]">{{ $link->name }}</span>
                        <iconify-icon icon="heroicons-outline:chevron-down" class="text-xs"></iconify-icon>
                    </a>

                    <ul class="absolute top-full left-0 mt-1 hidden bg-white dark:bg-slate-700 
                               text-gray-800 dark:text-gray-200 rounded shadow-lg py-2 min-w-[180px] z-50
                               group-hover:block">
                        <li>
                            <a
                                href="{{ route('qualityControls.show', $link) }}"
                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-600 
                                       {{ request()->is("qualityControls/{$link->id}") ? 'font-semibold' : '' }}">
                                {{ __('Ver detalles') }}
                            </a>
                        </li>
                        <li>
                            <a
                                href="{{ route('qualityControls.details', ['qualityControl' => $link, 'fase' => $link->getActiveFase()]) }}"
                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-600 
                                       {{ request()->is("qualityControls/{$link->id}/details/{$link->getActiveFase()}") ? 'font-semibold' : '' }}">
                                {{ __('Fase activa') }}
                            </a>
                        </li>
                        @foreach($link->fases as $fase)
                            <li>
                                <a
                                    href="{{ route('qualityControls.details', ['qualityControl' => $link, 'fase' => $fase->id]) }}"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-slate-600 
                                           {{ request()->is("qualityControls/{$link->id}/details/{$fase->id}") ? 'font-semibold' : '' }}">
                                    {{ $fase->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        @endhasrole
    </nav>
</div>
{{-- ... (abajo permanece igual) --}}

            <!-- end horizontal nav -->

            {{-- DERECHA: modo oscuro, menú de usuario, etc. --}}
            <div class="nav-tools flex items-center lg:space-x-5 space-x-3 rtl:space-x-reverse leading-0">
                <x-dark-light />
                <x-nav-user-dropdown />
                <button class="smallDeviceMenuController md:hidden block leading-0">
                    <iconify-icon class="cursor-pointer text-slate-900 dark:text-white text-2xl" icon="heroicons-outline:menu-alt-3"></iconify-icon>
                </button>
                <!-- end mobile menu -->
            </div>
            <!-- end nav tools -->
        </div>
    </div>
</div>

{{-- CSS extra para que el sub‐menú se muestre al hacer hover en el “.group” --}}
<style>
    @media (min-width: 768px) {
        .group:hover > ul {
            display: block !important;
        }
    }
</style>
