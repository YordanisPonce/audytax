<x-app-layout>
    <div>
        {{-- Breadcrumb start --}}
        <div class="mb-6">
            <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
        </div>
        {{-- Breadcrumb end --}}

        {{-- Update auditoryType form start --}}
        <form method="POST" enctype="multipart/form-data" class="max-w-xl m-auto" action="{{ route('auditoryTypes.update', $auditoryType) }}"
            class="max-w-4xl m-auto">
            @csrf
            @method('PUT')
           <x-form-auditory-type :auditoryType="$auditoryType" :label="__('Save')" :clients="$clients" />
        </form>
        {{-- Update auditoryType form end --}}
    </div>
</x-app-layout>
