@php
    $route = \Illuminate\Support\Facades\Route::current();
    $id = $route->parameter('id');
@endphp

<x-app-layout>
    <div class="pb-20">
        <div class=" mb-6 md:mb-2">
            {{-- Breadcrumb start --}}
            <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />

        </div>
        <form enctype="multipart/form-data" class="h-full d-flex flex-col" method="POST"
            action="{{ route('documents.save-files', $fase) }}">
            @csrf
            @foreach ($documents as $item)
                <x-file-picker :document="$item" :fileId="'input-file-' . $loop->iteration" :fileName="'files[' . $item->id . ']'" />
            @endforeach
            <div class="flex items-center justify-between mt-4">
                @if ($fase->isWaiting())
                    @hasrole('client')
                        <button type="submit" class="btn btn-primary btn-sm">Subir</button>
                    @endhasrole
                @endif
                <a href="{{ route('qualityControls.details', $fase->qualityControl) }}"
                    class="text-xs text-blue-500 float-right ml-auto flex items-center gap-2 md:hidden">Atr&aacute;s</a>
            </div>
        </form>
        {{-- Alert start --}}
        @if (session('message'))
            <x-alert :message="session('message')" :type="'success'" />
        @endif
        {{-- Alert end --}}
    </div>

    @push('scripts')
        <script type="module">
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
