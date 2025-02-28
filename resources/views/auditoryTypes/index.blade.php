<x-app-layout>
    <div>
        <div class=" mb-6">
            {{-- Breadcrumb start --}}
            <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />

        </div>

        {{-- Alert start --}}
        @if (session('message'))
            <x-alert :message="session('message')" :status="session('status')" :type="'success'" />
        @endif
        {{-- Alert end --}}

        {{-- Tarjeta principal que contiene la tabla de tipos de auditoría --}}
        <div class="card">
            {{-- Encabezado de la tarjeta con botones de acción --}}
            <header class="card-header noborder">
                <div class="justify-end flex gap-3 items-center flex-wrap">
                    {{-- Create Button start --}}
                    @can('auditoryType create')
                        <a class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3"
                            href="{{ route('auditoryTypes.create') }}">
                            <iconify-icon icon="ic:round-plus" class="text-lg mr-1">
                            </iconify-icon>
                            {{ __('New') }}
                        </a>
                    @endcan
                    {{-- Refresh Button start --}}
                    <a class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2.5"
                        href="{{ route('auditoryTypes.index') }}">
                        <iconify-icon icon="mdi:refresh" class="text-xl "></iconify-icon>
                    </a>
                </div>
                {{-- Barra de búsqueda --}}
                <div class="justify-center flex flex-wrap sm:flex items-center lg:justify-end gap-3">
                    <div class="relative w-full sm:w-auto flex items-center">
                        <form id="searchForm" method="get" action="{{ route('auditoryTypes.index') }}">
                            <input name="q" type="text"
                                class="inputField pl-8 p-2 border border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-900"
                                placeholder="Search" value="{{ request()->q }}">
                        </form>
                        <iconify-icon class="absolute text-textColor left-2 dark:text-white"
                            icon="quill:search-alt"></iconify-icon>
                    </div>
                </div>
            </header>

            {{-- Cuerpo de la tarjeta con la tabla de datos --}}
            <div class="card-body px-6 pb-6">
                <div class="overflow-x-auto -mx-6">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden ">
                            {{-- Tabla de tipos de auditoría --}}
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                {{-- Encabezados de la tabla --}}
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th ">
                                            {{ __('Name') }}
                                        </th>
                                        <th scope="col" class="table-th ">
                                            {{ __('Client') }}
                                        </th>
                                        <th scope="col" class="table-th ">
                                            {{ __('Documents') }}
                                        </th>
                                        {{-- Commented out Fases Amount column
                                        <th scope="col" class="table-th ">
                                            {{ __('Fases Amount') }}
                                        </th>
                                        --}}

                                        <th scope="col" class="table-th w-20">
                                            {{ __('Action') }}
                                        </th>
                                    </tr>
                                </thead>
                                {{-- Cuerpo de la tabla con los datos --}}
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse ($auditoryTypes as $auditoryType)
                                        <tr>
                                            {{-- Columna del nombre con enlace a detalles --}}
                                            <td class="table-td">
                                                <a href="{{ route('auditoryTypes.show', $auditoryType) }}">
                                                    {{ $auditoryType->name }}
                                                </a>
                                            </td>
                                            <td class="table-td">
                                                {{ $auditoryType->client->name ?? __('No Client') }}
                                            </td>
                                            <td class="table-td">
                                                {{ $auditoryType->fases->flatMap->documents->count() }}
                                            </td>
                                            {{-- Columna que muestra la cantidad de fases --}}
                                            {{-- Commented out Fases count
                                            <td class="table-td">
                                                {{ $auditoryType->fases_count }}
                                            </td>
                                            --}}
                                            {{-- Columna de acciones (ver, editar, eliminar) --}}
                                            <td class="table-td">
                                                <div class="flex space-x-3 rtl:space-x-reverse">
                                                    {{-- Botón para ver detalles --}}
                                                    @can('auditoryType show')
                                                        <a class="action-btn" href="{{ route('auditoryTypes.show', $auditoryType) }}">
                                                            <iconify-icon icon="heroicons:eye"></iconify-icon>
                                                        </a>
                                                    @endcan
                                                    {{-- Edit --}}
                                                    @can('auditoryType update')
                                                        <a class="action-btn"
                                                            href="{{ route('auditoryTypes.edit', $auditoryType) }}">
                                                            <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                        </a>
                                                    @endcan
                                                    {{-- delete --}}
                                                    @can('auditoryType delete')
                                                        <form id="deleteForm{{ $auditoryType->id }}" method="POST"
                                                            action="{{ route('auditoryTypes.destroy', $auditoryType) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a class="action-btn cursor-pointer"
                                                                onclick="sweetAlertDelete(event, 'deleteForm{{ $auditoryType->id }}')"
                                                                type="submit">
                                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                            </a>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        {{-- Mensaje cuando no hay resultados --}}
                                        <tr class="border border-slate-100 dark:border-slate-900 relative">
                                            <td class="table-cell text-center" colspan="5">
                                                <img src="{{ asset('images/result-not-found.svg') }}"
                                                    class="w-64 m-auto" />
                                                <h2 class="text-xl text-slate-700 mb-8 -mt-4">
                                                    {{ __('No results found.') }}</h2>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{-- Paginación de la tabla --}}
                            <x-table-footer :per-page-route-name="'auditoryTypes.index'" :data="$auditoryTypes" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts para la funcionalidad de eliminación --}}
    @push('scripts')
        <script>
            function sweetAlertDelete(event, formId) {
                event.preventDefault();
                let form = document.getElementById(formId);
                Swal.fire({
                    title: '@lang('Ejecturar Operacion?')',
                    icon: 'question',
                    showDenyButton: true,
                    confirmButtonText: '@lang('Delete')',
                    denyButtonText: '@lang('Cancel')',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            }
        </script>
    @endpush
</x-app-layout>
