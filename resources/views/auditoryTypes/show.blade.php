@php
    $params = ['auditorytype' => $auditoryType->id];
    $queryParams = http_build_query($params);
@endphp

<x-app-layout>
  <div class="mb-6">
    <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
  </div>

  {{-- Botón agregar fase --}}
  <div class="flex justify-end mb-4">
    @can('fase create')
      <a 
        href="{{ route('fases.create') . '?' . $queryParams }}" 
        class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3"
      >
        <iconify-icon icon="ic:round-plus" class="text-lg mr-1"></iconify-icon>
        {{ __('Nueva Fase') }}
      </a>
    @endcan
  </div>

  {{-- Lista de fases tipo acordeón --}}
  <div class="space-y-4 max-w-4xl mx-auto">
    @foreach($fases as $fase)
      <div class="border rounded-md overflow-hidden"> {{-- ✅ CORRECTO --}}


        {{-- Encabezado fase --}}
        <div class="flex justify-between items-center px-4 py-2 bg-gray-100 dark:bg-gray-800">
          {{-- Nombre de la fase (abre/cierra documentos) --}}
          <button onclick="toggleCollapse('collapse-fase-{{ $fase->id }}')" class="flex-1 text-left font-semibold"> {{-- NUEVO --}}
            {{ $fase->name }}
          </button>

          <div class="flex items-center space-x-2">
            {{-- Botón editar fase --}}
            @can('update', $fase)
              <a 
                href="{{ route('fases.edit', $fase) . '?' . $queryParams }}" 
                title="Editar fase" 
                class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded"
              >
                <iconify-icon icon="heroicons:pencil-square" class="text-lg" />
              </a>
            @endcan

            {{-- Botón eliminar fase --}}
            @can('delete', $fase)
              <form 
                id="deleteFaseForm{{ $fase->id }}" 
                action="{{ route('fases.destroy', $fase) }}" 
                method="POST" 
                class="inline"
              >
                @csrf @method('DELETE')
                <button 
                  type="button" 
                  onclick="sweetAlertDelete(event,'deleteFaseForm{{ $fase->id }}')" 
                  title="Eliminar fase"
                  class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded"
                >
                  <iconify-icon icon="heroicons:trash" class="text-lg" />
                </button>
              </form>
            @endcan

             {{-- Flecha colapsar --}}
            <button onclick="toggleCollapse('collapse-fase-{{ $fase->id }}')" class="p-1"> {{-- NUEVO --}}
              <svg class="h-5 w-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
          </div>
        </div>

        {{-- Documentos --}}
        {{-- Documentos --}}
        {{-- Documentos --}}
<div id="collapse-fase-{{ $fase->id }}" class="p-4 bg-white dark:bg-slate-700 hidden"> {{-- ✅ CAMBIO --}}

          {{-- Botón agregar documento --}}
          <div class="flex justify-end mb-2">
            @can('document create')
                        <a class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3"
                            href="{{ route('documents.create') . '?' . http_build_query(['fase' => $fase->id, 'auditorytype' => $auditoryType->id]) }}">
                            <iconify-icon icon="ic:round-plus" class="text-lg mr-1">
                            </iconify-icon>
                            {{ __('New') }}
                        </a>
                    @endcan
          </div>

          @if($fase->documents->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">No hay documentos en esta fase.</p>
          @else
            <ul class="space-y-2">
              @foreach($fase->documents as $doc)
                <li class="flex justify-between items-center bg-gray-50 dark:bg-slate-800 p-2 rounded">
                  <div class="flex-1">
                    <span class="font-medium">{{ $doc->name }}</span>
                    <span class="ml-4 text-sm text-gray-500">{{ $doc->status->label ?? 'Sin estado' }}</span>
                  </div>
                  <div class="flex items-center space-x-2">
                    @can('document update')
                      <a href="{{ route('documents.edit', $doc) . '?' . http_build_query(['fase' => $fase->id, 'auditorytype' => $auditoryType->id]) }}">
                        <iconify-icon icon="heroicons:pencil-square" />
                      </a>
                    @endcan
                    @if($doc->url)
                      @can('document download')
                        <a href="{{ route('documents.download', $doc) }}" title="Descargar">
                          <iconify-icon icon="ic:baseline-download" />
                        </a>
                      @endcan
                    @endif
                    @can('document delete')
                      <form id="deleteForm{{ $doc->id }}" method="POST" action="{{ route('documents.destroy', $doc) }}">
                        @csrf @method('DELETE')
                        <button 
                          type="button" 
                          onclick="sweetAlertDelete(event, 'deleteForm{{ $doc->id }}')"
                          title="Eliminar"
                        >
                          <iconify-icon icon="heroicons:trash" />
                        </button>
                      </form>
                    @endcan
                  </div>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>
    @endforeach
  </div>

  @push('scripts')
    <script type="text/javascript">
     // NUEVO: función para toggle collapse
      function toggleCollapse(id) {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('hidden');
      } 
      function sweetAlertDelete(event, formId) {
        event.preventDefault();
        const form = document.getElementById(formId);
        Swal.fire({
          title: '@lang("¿Estás seguro?")',
          icon: 'question',
          showDenyButton: true,
          confirmButtonText: '@lang("Eliminar")',
          denyButtonText: '@lang("Cancelar")',
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      }
    </script>
  @endpush
</x-app-layout>
