<x-app-layout>
    <div>
        {{-- Breadcrumb start --}}
        <div class="mb-6">
            <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
        </div>
        {{-- Breadcrumb end --}}

        {{-- Update user form start --}}
        <form method="POST" enctype="multipart/form-data" action="{{ route('users.update', $user) }}"
            class="max-w-4xl m-auto">
            @csrf
            @method('PUT')
            <x-form-user :roles="$roles" :user="$user" :label="__('Save Changes')" />
        </form>
        {{-- Update user form end --}}
    </div>
</x-app-layout>
