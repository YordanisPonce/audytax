<x-app-layout>
    <div>
        {{-- Breadcrumb start --}}
        <div class="mb-6">
            {{-- BreadCrumb --}}
            <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
        </div>
        {{-- Breadcrumb end --}}

        {{-- Create user form start --}}
        <form method="POST" action="{{ route('auditoryTypes.store') }}" enctype="multipart/form-data" class="max-w-xl m-auto">
            @csrf
            <x-form-auditory-type :auditoryType="false" :label="__('Save')" />
        </form>
        {{-- Create user form end --}}
    </div>
</x-app-layout>
