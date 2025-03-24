@php
    $queryParams = http_build_query(['fase' => $faseId]);
@endphp
<x-app-layout>
    <div>
        <div class=" mb-6">

            @if (auth()->user()->hasRole('client'))
                {{-- Breadcrumb client --}}
                <x-breadcrumb :breadcrumb-items="[['name' => 'Documentos', 'url' => '#', 'active' => true]]" :page-title="$pageTitle" />
            @else
                {{-- Breadcrumb start --}}
                <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
            @endif

        </div>

        {{-- Alert start --}}
        @if (session('message'))
            <x-alert :message="session('message')" :type="'success'" />
        @endif
        {{-- Alert end --}}


        <div class="card">
            <header class=" card-header noborder">
                <div class="justify-end flex gap-3 items-center flex-wrap">
                    {{-- Create Button start --}}
                    @if (auth()->user()->can('document create'))
                        <a class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3"
                            href="{{ route('documents.create') . '?' . $queryParams }}">
                            <iconify-icon icon="ic:round-plus" class="text-lg mr-1">
                            </iconify-icon>
                            {{ __('New') }}
                        </a>
                    @endif
                    {{-- Refresh Button start --}}
                    <a class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2.5 cursor-pointer"
                        onclick="location.reload()">
                        <iconify-icon icon="mdi:refresh" class="text-xl "></iconify-icon>
                    </a>
                </div>
                <div class="justify-center flex flex-wrap sm:flex items-center lg:justify-end gap-3">
                    <div class="relative w-full sm:w-auto flex items-center">
                        <form id="searchForm" method="get" action="{{ route('users.index') }}">
                            <input name="q" type="text"
                                class="inputField pl-8 p-2 border border-slate-200 dark:border-slate-700 rounded-md dark:bg-slate-900"
                                placeholder="Search" value="{{ request()->q }}">
                        </form>
                        <iconify-icon class="absolute text-textColor left-2 dark:text-white"
                            icon="quill:search-alt"></iconify-icon>
                    </div>
                </div>
            </header>
            <div class="card-body px-6 pb-6">
                <div class="overflow-x-auto -mx-6">
                    <div class="inline-block min-w-full align-middle">
                        <div class="overflow-hidden ">
                            <table class="min-w-full divide-y divide-slate-100 table-fixed dark:divide-slate-700">
                                <thead class="bg-slate-200 dark:bg-slate-700">
                                    <tr>
                                        <th scope="col" class="table-th ">
                                            {{ __('Name') }}
                                        </th>
                                        <th scope="col" class="table-th ">
                                            {{ __('Status') }}
                                        </th>
                                        {{-- @if (auth()->user()->hasRole('admin'))
                                            <th scope="col" class="table-th ">
                                                {{ __('Aprobacion') }}
                                            </th>
                                        @endif --}}
                                        {{-- <th scope="col" class="table-th ">
                                            {{ __('Fase') }}
                                        </th> --}}
                                        <th scope="col" class="table-th w-20">
                                            {{ __('Action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @php
                                        $pendingDocuments = $documents->filter(function ($doc) {
                                            return !$doc->isApproved();
                                        });
                                        $approvedDocuments = $documents->filter(function ($doc) {
                                            return $doc->isApproved();
                                        });
                                        $sortedDocuments = $pendingDocuments->merge($approvedDocuments);
                                    @endphp
                                    @forelse ($sortedDocuments as $document)
                                        @if (auth()->user()->hasRole('client') ? $document->isApproved() : true)
                                            <tr>
                                                <td class="table-td">
                                                    {{ $document->name }}
                                                </td>
                                                <td class="table-td">
                                                    {{ $document->status->label }}
                                                </td>
                                                {{-- @if (auth()->user()->hasRole('admin'))
                                                    <td class="table-td">
                                                        @if ($document->isApproved())
                                                            <span
                                                                class="badge bg-success-500 text-white capitalize">{{ __('Aprobado') }}</span>
                                                        @else
                                                            <span
                                                                class="badge bg-warning-500 text-white capitalize">{{ __('Pendiente') }}</span>
                                                        @endif
                                                    </td>
                                                @endif --}}
                                                {{-- <td class="table-td">
                                                {{ $document->fase->name }}
                                            </td> --}}
                                                <td class="table-td">
                                                    <div class="flex space-x-3 rtl:space-x-reverse">
                                                        {{-- @if (auth()->user()->can('document update'))
                                                            <a class="action-btn"
                                                                href="{{ route('documents.edit', ['document' => $document]) . '?' . $queryParams }}">
                                                                <iconify-icon
                                                                    icon="heroicons:pencil-square"></iconify-icon>
                                                            </a>
                                                        @endif --}}
                                                        {{-- @if ($document->url)
                                                            @can('document download')
                                                                <a class="action-btn"
                                                                    href="{{ route('documents.download', ['document' => $document]) }}">
                                                                    <iconify-icon
                                                                        icon="ic:baseline-download"></iconify-icon>
                                                                </a>
                                                            @endcan
                                                        @endif --}}
                                                        {{-- delete --}}
                                                        @if (auth()->user()->can('document delete'))
                                                            <form id="deleteForm{{ $document->id }}" method="POST"
                                                                action="{{ route('documents.destroy', $document) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a class="action-btn cursor-pointer"
                                                                    onclick="sweetAlertDelete(event, 'deleteForm{{ $document->id }}')"
                                                                    type="submit">
                                                                    <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                                </a>
                                                            </form>
                                                        @endif

                                                            {{-- Ver archivos del documento --}}
                                                        <a class="action-btn" href="{{ route('files.index', ['document' => $document->id]) }}">
                                                            <iconify-icon icon="heroicons:document-duplicate"></iconify-icon>
                                                        </a>

                                                        {{-- Botón de upload de archivos directo --}}
                                                        @if (!auth()->user()->hasRole('admin'))
                                                            <form id="uploadForm{{ $document->id }}" action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data" class="inline">
                                                                @csrf
                                                                <input type="hidden" name="document_id" value="{{ $document->id }}">
                                                                <input type="file" name="file" id="fileInput{{ $document->id }}" class="hidden" onchange="document.getElementById('uploadForm{{ $document->id }}').submit()">
                                                                <a class="action-btn cursor-pointer" onclick="document.getElementById('fileInput{{ $document->id }}').click()">
                                                                    <iconify-icon icon="heroicons:arrow-up-tray"></iconify-icon>
                                                                </a>
                                                            </form>
                                                        @endif

                                                    </div>
                                                </td>
                                          </tr>
                                        @endif
                                    @empty
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
                            <x-table-footer :per-page-route-name="'documents.index'" :data="$documents" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- El modal de subir archivos ha sido eliminado y reemplazado por un botón directo --}}
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function sweetAlertDelete(event, formId) {
                event.preventDefault();
                let form = document.getElementById(formId);
                Swal.fire({
                    title: '@lang('Are you sure ? ')',
                    icon: 'question',
                    showDenyButton: true,
                    confirmButtonText: '@lang('Delete ')',
                    denyButtonText: '@lang('Cancel ')',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            }

            // Las funciones del modal han sido eliminadas ya que ahora la subida es directa
        </script>
    @endpush
</x-app-layout>
