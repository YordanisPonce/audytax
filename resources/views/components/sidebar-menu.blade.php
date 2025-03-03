@props(['links'])

@php
    $qualityControl = request()->query('qualityControl');
    $id = last(request()->segments());
@endphp

<!-- BEGIN: Sidebar -->
<div class="sidebar-wrapper group w-0 hidden xl:w-[248px] xl:block">
    <div id="bodyOverlay" class="w-screen h-screen fixed top-0 bg-slate-900 bg-opacity-50 backdrop-blur-sm z-10 hidden">
    </div>
    <div class="logo-segment">

        <!-- Application Logo -->
        <x-application-logo />

        <!-- Sidebar Type Button -->
        {{-- <div id="sidebar_type" class="cursor-pointer text-slate-900 dark:text-white text-lg">
            <iconify-icon class="sidebarDotIcon extend-icon text-slate-900 dark:text-slate-200"
                icon="fa-regular:dot-circle"></iconify-icon>
            <iconify-icon class="sidebarDotIcon collapsed-icon text-slate-900 dark:text-slate-200"
                icon="material-symbols:circle-outline"></iconify-icon>
        </div>
        <button class="sidebarCloseIcon text-2xl inline-block md:hidden">
            <iconify-icon class="text-slate-900 dark:text-slate-200" icon="clarity:window-close-line"></iconify-icon>
        </button> --}}
    </div>
    <div id="nav_shadow"
        class="nav_shadow h-[60px] absolute top-[80px] nav-shadow z-[1] w-full transition-all duration-200 pointer-events-none
      opacity-0">
    </div>
    <div class="sidebar-menus bg-white dark:bg-slate-800 py-2 px-4 h-[calc(100%-80px)] z-50" id="sidebar_menus">
        <ul class="sidebar-menu h-full">
            <li class="sidebar-menu-title flex justify-between items-center">
                <span>
                    {!! __('MEN&Uacute;') !!}
                </span>
                @unlessrole('admin')
                    <span>
                        {{ $links->links() }}
                    </span>
                @endunlessrole
            </li>
            <li>
                <a href="{{ route('dashboard.index') }}"
                    class="navItem {{ request()->is('dashboard*') ? 'active' : '' }}">
                    <span class="flex items-center">
                        <iconify-icon class=" nav-icon" icon="heroicons-outline:home"></iconify-icon>
                        <span>{{ __('Inicio') }}</span>
                    </span>
                </a>
            </li>
            
            <!-- Auditorias para clientes -->
            @hasrole('client')
                @php
                    $clientAudits = \App\Models\AuditoryType::where('client_id', auth()->id())->get();
                @endphp
                
                @if($clientAudits->count() > 0)
                    <li class="sidebar-menu-title mt-2">
                        <span>{{ __('Mis Auditorías') }}</span>
                    </li>
                    
                    @foreach($clientAudits as $audit)
                        <li>
                            <a href="{{ route('auditoryTypes.show', $audit) }}"
                                class="navItem {{ request()->is('auditoryTypes/'.$audit->id) ? 'active' : '' }}">
                                <span class="flex items-center">
                                    <iconify-icon class=" nav-icon" icon="fluent-mdl2:compliance-audit"></iconify-icon>
                                    <span class="truncate">{{ $audit->name }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                @endif
            @endhasrole
            
            {{--             <!-- Database -->
            <li>
                <a href="{{ route('database-backups.index') }}"
                    class="navItem {{ request()->is('database-backups*') ? 'active' : '' }}">
                    <span class="flex items-center">
                        <iconify-icon class=" nav-icon" icon="iconoir:database-backup"></iconify-icon>
                        <span>{{ __('Database Backup') }}</span>
                    </span>
                </a>
            </li> --}}
            <!-- Settings -->
            {{--             <li>
                <a href="{{ route('general-settings.show') }}"
                    class="navItem {{ request()->is('general-settings*') || request()->is('roles*') || request()->is('profiles*') || request()->is('permissions*') ? 'active' : '' }}">
                    <span class="flex items-center">
                        <iconify-icon class=" nav-icon" icon="material-symbols:settings-outline"></iconify-icon>
                        <span>{{ __('Settings') }}</span>
                    </span>
                </a>
            </li> --}}
            
            
            
                


            @hasrole('admin')
                @can('user index')
                    <li>
                        <a href="{{ route('users.index') }}"
                            class="navItem {{ request()->is('users.*') || request()->is('users*') ? 'active' : '' }}">
                            <span class="flex items-center">
                                <iconify-icon class=" nav-icon" icon="mdi:users"></iconify-icon>
                                <span>{{ __('Usuarios') }}</span>
                            </span>
                        </a>
                    </li>
                @endcan

                <!-- auditoryTypes -->
                @can('auditoryType index')
                    <li>
                        <a href="{{ route('auditoryTypes.index') }}"
                            class="navItem {{ ((request()->is('fases.*') || request()->is('fases*')) && !$qualityControl) || request()->is('auditoryTypes.*') || request()->is('auditoryTypes*') ? 'active' : '' }}">
                            <span class="flex items-center">
                                <iconify-icon class=" nav-icon" icon="fluent-mdl2:compliance-audit"></iconify-icon>
                                <span class="truncate">{{ __('Auditory Type') }}</span>
                            </span>
                        </a>
                    </li>
                @endcan

                <!-- qualityControl -->
                {{-- @can('qualityControl index')
                    <li>
                        <a href="{{ route('qualityControls.index') }}"
                            class="navItem {{ request()->is('qualityControls.*') || request()->is('qualityControls*') || request()->is('qualityControls.*') || request()->is('qualityControls*') || $qualityControl ? 'active' : '' }}">
                            <span class="flex items-center">
                                <iconify-icon class=" nav-icon" icon="icon-park-twotone:inspection"></iconify-icon>
                                <span class="truncate">{{ __('Quality Controls') }}</span>
                            </span>
                        </a>
                    </li>
                @endcan
            @else
                @isset($links)
                    @foreach ($links as $link)
                        @php
                            $fases = $link
                                ->fases()
                                ->get(['id'])
                                ->pluck('id')
                                ->toArray();
                        @endphp
                        <li>
                            <a href="{{ route('qualityControls.details', ['qualityControl' => $link, 'fase' => $link->getActiveFase()]) }}"
                                @class([
                                    'navItem',
                                    'active' =>
                                        $id == $link->id ||
                                        (request()->is('comments*') && in_array($id, $fases)),
                                ])>
                                <span class="flex items-center truncate">
                                    <iconify-icon class=" nav-icon" icon="icon-park-twotone:inspection"></iconify-icon>
                                    <span class="truncate">{{ $link->name }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                @endisset --}}



            @endhasrole
        </ul>
        <!-- Upgrade Your Business Plan Card Start -->
    </div>
</div>
<!-- End: Sidebar -->
