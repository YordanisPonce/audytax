<x-app-layout>
    <div>
        {{-- Breadcrumb start --}}
        <div class="mb-6">
            {{-- BreadCrumb --}}
            <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
        </div>
        {{-- Breadcrumb end --}}

        {{-- Create user form start --}}
        <form method="POST" action="{{ route('qualityControls.store') }}" enctype="multipart/form-data"
            class="max-w-4xl m-auto">
            @csrf
            <x-form-quality-control :qualityControl="false" :auditoryTypes="$auditoryTypes" :consultants="$consultants" :clients="$clients"
                :statuses="$statuses" :label="__('Save')" />
        </form>
        {{-- Create user form end --}}
    </div>
    @push('scripts')
{{--         @vite(['resources/js/plugins/Select2.min.js'])  --}}
      {{--   <script src="{{ asset('plugins/Select2.min.js') }}"></script> --}}

        <script type="module">
            // Form Select Area
            $(".select2").select2({
                placeholder: "Select an Option",
            });

            $("#limitedSelect").select2({
                placeholder: "Select an Option",
                maximumSelectionLength: 2,
            });
        </script>
    @endpush
</x-app-layout>
