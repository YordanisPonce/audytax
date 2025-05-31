<x-app-layout>
  <div>
    {{-- Breadcrumb --}}
    <div class="mb-6">
      <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
    </div>

    {{-- Formulario de edición --}}
    <form
      method="POST"
      action="{{ route('fases.update', $fase) }}"
      class="max-w-4xl m-auto"
      enctype="multipart/form-data"
    >
      @csrf
      @method('PUT')

      {{-- Llamamos al componente, enviándole la Fase y los auditoryTypes --}}
      <x-form-fase
        :fase="$fase"
        :auditoryTypes="$auditoryTypes"
        :label="__('Guardar cambios')"
      />
    </form>
  </div>
</x-app-layout>
