{{-- resources/views/components/dashboard-header.blade.php --}}
@props(['links']) {{-- Recibe la colección de QualityControls (u otra) --}}

@php
    $qualityControl = request()->query('qualityControl');
    $id = last(request()->segments());
@endphp

<div class="z-[9] sticky top-0" id="app_header">
    <div class="app-header z-[999] bg-white dark:bg-slate-800 shadow-sm dark:shadow-slate-700 !ml-0  margin-0">
        <div class="flex px-20 justify-between items-center h-full">

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
        <ul class="flex space-x-4 list-none">
        {{-- Botón Inicio con mismo estilo que el resto --}}
        <li>
        <a
        
            href="{{ route('dashboard.index') }}"
            class="flex items-center space-x-1 px-3 py-2 rounded 
                   hover:bg-gray-100 dark:hover:bg-slate-700 
                   {{ request()->is('dashboard*') ? 'bg-gray-200 dark:bg-slate-600 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
            <iconify-icon icon="heroicons-outline:home" class="text-lg"></iconify-icon>
            <span>{{ __('Inicio') }}</span>
        </a>
        </li>

    @hasrole('admin')
        @can('user index')
            <li>
                <a href="{{ route('users.index') }}"
                   class=" flex items-center space-x-1 px-3 py-2 rounded 
                   hover:bg-gray-100 dark:hover:bg-slate-700  navItem {{ request()->is('users.*') || request()->is('users*') ? 'active' : '' }}">
                    <span class="flex items-center">
                        <iconify-icon class="nav-icon" icon="mdi:users"></iconify-icon>
                        <span>{{ __('Usuarios') }}</span>
                    </span>
                </a>
            </li>
        @endcan

        <!-- auditoryTypes -->
        @can('auditoryType index')
            <li>
                <a href="{{ route('auditoryTypes.index') }}"
                   class="flex items-center space-x-1 px-3 py-2 rounded 
                   hover:bg-gray-100 dark:hover:bg-slate-700  navItem {{ ((request()->is('fases.*') || request()->is('fases*')) && !$qualityControl) || request()->is('auditoryTypes.*') || request()->is('auditoryTypes*') ? 'active' : '' }}">
                    <span class="flex items-center">
                        <iconify-icon class="nav-icon" icon="fluent-mdl2:compliance-audit"></iconify-icon>
                        <span class="truncate">{{ __('Auditory Type') }}</span>
                    </span>
                </a>
            </li>
        @endcan

        <!-- qualityControls -->
        @can('qualityControl index')
            <li>
                <a href="{{ route('qualityControls.index') }}"
                   class="flex items-center space-x-1 px-3 py-2 rounded 
                   hover:bg-gray-100 dark:hover:bg-slate-700  navItem {{ request()->is('qualityControls.*') || request()->is('qualityControls*') || $qualityControl ? 'active' : '' }}">
                    <span class="flex items-center">
                        <iconify-icon class="nav-icon" icon="icon-park-twotone:inspection"></iconify-icon>
                        <span class="truncate">{{ __('Quality Controls') }}</span>
                    </span>
                </a>
            </li>
        @endcan
    @else
    @isset($links)
        @foreach($links as $link)
            @php
                $fases   = $link->fases->pluck('id')->toArray();
                $isActive = $id == $link->id || (request()->is('comments*') && in_array($id, $fases));
            @endphp
            <li>
                <a
                    href="{{ route('qualityControls.details', ['qualityControl' => $link, 'fase' => $link->getActiveFase()]) }}"
                    class="flex items-center space-x-1 px-3 py-2 rounded 
                           hover:bg-gray-100 dark:hover:bg-slate-700 
                           {{ $isActive 
                                ? 'bg-gray-200 dark:bg-slate-600 font-semibold' 
                                : 'text-gray-700 dark:text-gray-300' }}">
                    <iconify-icon icon="icon-park-twotone:inspection" class="text-lg"></iconify-icon>
                    <span class="truncate">{{ $link->name }}</span>
                </a>
            </li>
        @endforeach
    @endisset
@endhasrole

</ul>

    </nav>
</div>
{{-- ... (abajo permanece igual) --}}

            <!-- end horizontal nav -->

            {{-- DERECHA: modo oscuro, menú de usuario, etc. --}}
            <div class="nav-tools flex items-center lg:space-x-5 space-x-3 rtl:space-x-reverse leading-0">
                @hasrole('admin')
    <!-- Botón campanita -->
    <div class="relative">
        <button id="notifBtn" …>
            <iconify-icon icon="heroicons-outline:bell" class="text-xl …"></iconify-icon>
        </button>

        <!-- Dropdown oculto inicialmente -->
        <div id="notifDropdown" class="hidden top-5 absolute right-0 w-80 bg-white dark:bg-slate-800 shadow-lg rounded-lg z-50">
            <div class="px-4 py-2 border-b dark:border-slate-700  ">
                <span class="font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('Notificaciones') }}</span>
            </div>

            @if($notifications->isEmpty())
                <div class="px-4 py-3 text-gray-600 dark:text-gray-400 text-sm">
                    {{ __('No new notifications.') }}
                </div>
            @else
                <ul>
                    @foreach($notifications as $note)
                        <li class="px-4 py-3 hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                            <div class="flex items-start space-x-2">
                                <img
                                    src="{{ $note->user->avatar ?: Avatar::create($note->user->name)->setDimension(60)->setFontSize(40)->toBase64() }}"
                                    class="h-8 w-8 rounded-full object-cover"
                                    alt="Avatar"
                                >
                                <div class="flex-1">
                                    <p class="text-sm text-gray-800 dark:text-gray-200">
                                        {{ $note->description }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{-- {{ $note->created_at->diffForHumans() }} --}}
                                    </p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            {{-- <div class="px-4 py-2 border-t dark:border-slate-700 text-right">
                <a href="{{ route('qualityControls.index') }}"
                   class="text-blue-600 hover:underline text-sm">
                    {{ __('View all') }}
                </a>
            </div> --}}
        </div>
    </div>
@endhasrole

                
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn      = document.getElementById('notifBtn');
        const dropdown = document.getElementById('notifDropdown');

        if (btn && dropdown) {
            btn.addEventListener('click', e => {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', () => {
                if (!dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            });

            dropdown.addEventListener('click', e => {
                e.stopPropagation();
            });
        }
    });
</script>
@endpush

