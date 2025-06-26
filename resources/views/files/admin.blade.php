<x-app-layout>
    <div>
        <div class="mb-6">
            {{-- Breadcrumb start --}}
            <x-breadcrumb :breadcrumb-items="[['name' => 'Administración', 'url' => '#', 'active' => false], ['name' => 'Archivos por Cliente', 'url' => '#', 'active' => true]]" :page-title="'Archivos por Cliente'" />
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
                    {{-- Refresh Button --}}
                    <a class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2.5 cursor-pointer"
                        onclick="location.reload()">
                        <iconify-icon icon="mdi:refresh" class="text-xl"></iconify-icon>
                    </a>
                </div>
            </header>
            <div class="card-body px-6 pb-6">
                {{-- Acordeón de clientes --}}
                <div class="space-y-5">
                    @forelse ($filesByClient as $clientId => $clientFiles)
                        <div class="rounded-md border border-slate-200 dark:border-slate-700">
                            <div class="cursor-pointer border-b border-slate-200 dark:border-slate-700 transition duration-150 font-medium w-full text-start flex justify-between items-center p-4 bg-slate-50 dark:bg-slate-700 rounded-t-md" 
                                 onclick="toggleAccordion('client-{{ $clientId }}')">
                                <span>{{ $clientFiles->first()->user->name }} ({{ $clientFiles->count() }} archivos)</span>
                                <span class="text-slate-900 dark:text-white">
                                    <iconify-icon icon="heroicons-outline:chevron-down" class="client-{{ $clientId }}-icon"></iconify-icon>
                                </span>
                            </div>
                            <div id="client-{{ $clientId }}-content" class="hidden p-4 text-slate-600 dark:text-slate-300">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                        <thead class="bg-slate-200 dark:bg-slate-700">
                                            <tr>
                                                <th scope="col" class="table-th">{{ __('Nombre') }}</th>
                                                <th scope="col" class="table-th">{{ __('Documento') }}</th>
                                                <th scope="col" class="table-th">{{ __('Tipo de Auditoría') }}</th>
                                                <th scope="col" class="table-th">{{ __('Estado') }}</th>
                                                <th scope="col" class="table-th">{{ __('Fecha') }}</th>
                                                <th scope="col" class="table-th w-20">{{ __('Acciones') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                            @foreach ($clientFiles as $file)
                                                <tr>
                                                    <td class="table-td">{{ $file->name }}</td>
                                                    <td class="table-td">
                                                        <a href="{{ route('files.index', ['document' => $file->document_id]) }}" class="text-primary-500 hover:text-primary-600">
                                                            {{ $file->document->name }}
                                                        </a>
                                                    </td>
                                                    <td class="table-td">{{ $file->document->auditoryType->name }}</td>
                                                    <td class="table-td">
                                                        @if ($file->isApproved())
                                                            <span class="badge bg-success-500 text-white capitalize">{{ __('Aprobado') }}</span>
                                                        @else
                                                            <span class="badge bg-warning-500 text-white capitalize">{{ __('Pendiente') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="table-td">{{ $file->created_at->format('d/m/Y H:i') }}</td>
                                                    <td class="table-td">
                                                        <div class="flex space-x-3 rtl:space-x-reverse">
                                                            {{-- Descargar archivo --}}
                                                            <a class="action-btn" href="{{ route('files.download', ['file' => $file]) }}">
                                                                <iconify-icon icon="ic:baseline-download"></iconify-icon>
                                                            </a>
                                                            
                                                            {{-- Aprobar/Rechazar --}}
                                                            @if (!$file->isApproved())
                                                                <a class="action-btn text-success-500" href="{{ route('files.approve', ['file' => $file]) }}">
                                                                    <iconify-icon icon="heroicons:check"></iconify-icon>
                                                                </a>
                                                            @else
                                                                <a class="action-btn text-warning-500" href="{{ route('files.reject', ['file' => $file]) }}">
                                                                    <iconify-icon icon="heroicons:x-mark"></iconify-icon>
                                                                </a>
                                                            @endif
                                                            
                                                            {{-- Eliminar --}}
                                                            <form id="deleteForm{{ $file->id }}" method="POST" action="{{ route('files.destroy', $file) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a class="action-btn cursor-pointer text-danger-500"
                                                                    onclick="sweetAlertDelete(event, 'deleteForm{{ $file->id }}')">
                                                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                                </a>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <img src="{{ asset('images/result-not-found.svg') }}" class="w-64 m-auto" />
                            <h2 class="text-xl text-slate-700 mb-8 -mt-4">{{ __('No se encontraron archivos.') }}</h2>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleAccordion(clientId) {
                const content = document.getElementById(clientId + '-content');
                const icon = document.querySelector('.' + clientId + '-icon');
                
                if (content.classList.contains('hidden')) {
                    content.classList.remove('hidden');
                    icon.setAttribute('icon', 'heroicons-outline:chevron-up');
                } else {
                    content.classList.add('hidden');
                    icon.setAttribute('icon', 'heroicons-outline:chevron-down');
                }
            }
            
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