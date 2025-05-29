<x-app-layout>
    <div>
        {{-- Breadcrumb start --}}
        <div class="mb-6">
            <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
        </div>
        {{-- Breadcrumb end --}}

        {{-- Update user form start --}}
        <form method="POST" enctype="multipart/form-data" action="{{ route('qualityControls.update', $qualityControl) }}"
            class="max-w-4xl m-auto">
            @csrf
            @method('PUT')
            <x-form-quality-control :qualityControl="$qualityControl" :auditoryTypes="$auditoryTypes" :consultants="$consultants" :clients="$clients" :statuses="$statuses" :label="__('Save')" />
        </form>
        {{-- Update user form end --}}
    </div>
    @include('qualityControls.partials.js')
</x-app-layout>
