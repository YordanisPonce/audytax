@php
    $queryParams = http_build_query(['document' => $document->id]);
@endphp
<x-app-layout>
    <div>
        <div class="mb-6">
            @if (auth()->user()->hasRole('client'))
                {{-- Breadcrumb client --}}
                <x-breadcrumb :breadcrumb-items="[['name' => 'Archivos', 'url' => '#', 'active' => true]]" :page-title="$pageTitle" />
            @else
                {{-- Breadcrumb start --}}
                <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
            @endif
        </div>

        {{-- Alert start --}}
        @if (session('message'))
            <x-alert :message="session('message')" :type="'success'" />
        @endif
        @if (session('error'))
            <x-alert :message="session('error')" :type="'danger'" />
        @endif
        {{-- Alert end --}}

        <div class="card">
            <header class="card-header noborder">
                <div class="justify-end flex gap-3 items-center flex-wrap">
                    {{-- Volver al documento --}}
                    <a class="btn inline-flex justify-center btn-outline-dark rounded-[25px] items-center !p-2 !px-3"
                        href="{{ route('auditoryTypes.show', ['auditoryType' => $document->fase->auditoryType]) }}">
                        <iconify-icon icon="heroicons:arrow-left" class="text-lg mr-1"></iconify-icon>
                        {{ __('Volver') }}
                    </a>
                    {{-- Refresh Button --}}
                    <a class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2.5 cursor-pointer"
                        onclick="location.reload()">
                        <iconify-icon icon="mdi:refresh" class="text-xl"></iconify-icon>
                    </a>
                </div>
            </header>
            <div class="card-body px-6 pb-6">
                {{-- Formulario para subir archivos (solo visible para clientes) --}}
                @if (!auth()->user()->hasRole('admin'))
                <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-700 rounded-md">
                    <h3 class="text-lg font-medium mb-3">{{ __('Subir nuevo archivo') }}</h3>
                    <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap gap-3 items-end">
                        @csrf
                        <input type="hidden" name="document_id" value="{{ $document->id }}">
                        <div class="flex-1">
                            <label for="file" class="form-label">{{ __('Archivo') }}</label>
                            <input type="file" name="file" id="file" class="form-control" required>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-dark">
                                <iconify-icon icon="heroicons:arrow-up-tray" class="text-lg mr-1"></iconify-icon>
                                {{ __('Subir') }}
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                {{-- Tabla de archivos --}}
                <div class="overflow-x-auto -mx-6">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th">
                                            {{ __('Nombre') }}
                                        </th>
                                        @if (auth()->user()->hasRole('admin'))
                                            <th scope="col" class="table-th">
                                                {{ __('Cliente') }}
                                            </th>
                                        @endif
                                        <th scope="col" class="table-th">
                                            {{ __('Estado') }}
                                        </th>
                                        <th scope="col" class="table-th">
                                            {{ __('Fecha') }}
                                        </th>
                                        <th scope="col" class="table-th w-20">
                                            {{ __('Acciones') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse ($files as $file)
                                        <tr>
                                            <td class="table-td">
                                                {{ $file->name }}
                                            </td>
                                            @if (auth()->user()->hasRole('admin'))
                                                <td class="table-td">
                                                    {{ $file->user->name }}
                                                </td>
                                            @endif
                                            <td class="table-td">
                                                @if ($file->isApproved())
                                                    <span class="badge bg-success-500 text-white capitalize">{{ __('Aprobado') }}</span>
                                                @else
                                                    <span class="badge bg-warning-500 text-white capitalize">{{ __('Pendiente') }}</span>
                                                @endif
                                            </td>
                                            <td class="table-td">
                                                {{ $file->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="table-td">
                                                <div class="flex space-x-3 rtl:space-x-reverse">
                                                    {{-- Descargar archivo --}}
                                                    <a class="action-btn" href="{{ route('files.download', ['file' => $file]) }}">
                                                        <iconify-icon icon="ic:baseline-download"></iconify-icon>
                                                    </a>
                                                    
                                                    {{-- Aprobar/Rechazar (solo admin) --}}
                                                    @if (auth()->user()->hasRole('admin'))
                                                        @if (!$file->isApproved())
                                                            <a class="action-btn text-success-500" href="{{ route('files.approve', ['file' => $file]) }}">
                                                                <iconify-icon icon="heroicons:check"></iconify-icon>
                                                            </a>
                                                        @else
                                                            <a class="action-btn text-warning-500" href="{{ route('files.reject', ['file' => $file]) }}">
                                                                <iconify-icon icon="heroicons:x-mark"></iconify-icon>
                                                            </a>
                                                        @endif
                                                    @endif
                                                    
                                                    {{-- Eliminar (admin o propietario) --}}
                                                    @if (auth()->user()->hasRole('admin') || $file->user_id == auth()->id())
                                                        <form id="deleteForm{{ $file->id }}" method="POST" action="{{ route('files.destroy', $file) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a class="action-btn cursor-pointer text-danger-500"
                                                                onclick="sweetAlertDelete(event, 'deleteForm{{ $file->id }}')">
                                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                            </a>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="border border-slate-100 dark:border-slate-900 relative">
                                            <td class="table-cell text-center" colspan="{{ auth()->user()->hasRole('admin') ? 6 : 5 }}">
                                                <img src="{{ asset('images/result-not-found.svg') }}" class="w-64 m-auto" />
                                                <h2 class="text-xl text-slate-700 mb-8 -mt-4">{{ __('No se encontraron archivos.') }}</h2>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function sweetAlertDelete(event, formId) {
                event.preventDefault();
                let form = document.getElementById(formId);
                Swal.fire({
                    title: '@lang("¿Estás seguro?")',
                    text: '@lang("Esta acción no se puede deshacer")',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '@lang("Sí, eliminar")',
                    cancelButtonText: '@lang("Cancelar")',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>