<x-app-layout>
    <div>
        {{-- Breadcrumb start --}}
        <div class="mb-6">
            {{-- BreadCrumb --}}
            <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
        </div>
        {{-- Breadcrumb end --}}

        {{-- Create user form start --}}
        <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" class="max-w-4xl m-auto">
            @csrf
            <x-form-user :roles="$roles" :user="false" :label="__('Save')" />
        </form>
        {{-- Create user form end --}}
    </div>
</x-app-layout>
