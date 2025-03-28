@php
    $params = ['auditorytype' => $auditoryId];
    if (isset($qualityControlId)) {
        $params = array_merge($params, ['qualityControl' => $qualityControlId]);
    }
    $queryParams = http_build_query($params);
@endphp
<x-app-layout>
    <div>
        <div class=" mb-6">
            {{-- Breadcrumb start --}}
            <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />

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
                    @can('fase create')
                        <a class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3"
                            href="{{ route('fases.create') . '?' . $queryParams }}">
                            <iconify-icon icon="ic:round-plus" class="text-lg mr-1">
                            </iconify-icon>
                            {{ __('New') }}
                        </a>
                    @endcan
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
                                            {{ __('Documents') }}
                                        </th>
                                        <th scope="col" class="table-th ">
                                            {{ __('Auditory Type') }}
                                        </th>
                                        <th scope="col" class="table-th ">
                                            {{ __('Status') }}
                                        </th>
                                        <th scope="col" class="table-th w-20">
                                            {{ __('Action') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-slate-100 dark:bg-slate-800 dark:divide-slate-700">
                                    @forelse ($fases as $fase)
                                    @php
                                    $param = '';
                                        if (isset($fase->qualityControl)) {
                                            $param =  http_build_query(['qualityControl'=>$fase->qualityControl->id]);
                                        }
                                    @endphp
                                        <tr>
                                            <td class="table-td">
                                                <a href="{{ route('fases.show', $fase) . '?' . $param }}">{{ $fase->name }}</a>
                                            </td>
                                            <td class="table-td">
                                                @forelse($fase->documents as $document)
                                                    <div>{{ $document->name }}</div>
                                                @empty
                                                    <div>{{ __('No documents') }}</div>
                                                @endforelse
                                            </td>
                                            <td class="table-td">
                                                {{ $fase->auditoryType->name }}
                                            </td>
                                            <td class="table-td">
                                                {{ $fase->status->label ?? 'No definido' }}
                                            </td>
                                            <td class="table-td">
                                                <div class="flex space-x-3 rtl:space-x-reverse">
                                                    {{-- view --}}
                                                    @can('fase show')
                                                        <a class="action-btn" href="{{ route('fases.show', $fase) . '?' . $param }}">
                                                            <iconify-icon icon="heroicons:eye"></iconify-icon>
                                                        </a>
                                                    @endcan
                                                    {{-- Edit --}}
                                                    @can('fase update')
                                                        <a class="action-btn"
                                                            href="{{ route('fases.edit', ['fase' => $fase]) . '?' . $queryParams }}">
                                                            <iconify-icon icon="heroicons:pencil-square"></iconify-icon>
                                                        </a>
                                                    @endcan
                                                    {{-- delete --}}
                                                    @can('fase delete')
                                                        <form id="deleteForm{{ $fase->id }}" method="POST"
                                                            action="{{ route('fases.destroy', $fase) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <a class="action-btn cursor-pointer"
                                                                onclick="sweetAlertDelete(event, 'deleteForm{{ $fase->id }}')"
                                                                type="submit">
                                                                <iconify-icon icon="heroicons:trash"></iconify-icon>
                                                            </a>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="border border-slate-100 dark:border-slate-900 relative">
                                            <td class="table-cell text-center" colspan="5">
                                                <img src="{{ asset('images/result-not-found.svg') }}" class="w-64 m-auto" />
                                                <h2 class="text-xl text-slate-700 mb-8 -mt-4">
                                                    {{ __('No results found.') }}</h2>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <x-table-footer :per-page-route-name="'fases.index'" :data="$fases" />
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
        </script>
    @endpush
</x-app-layout>
