@php
    // Construimos la query string para pasarla a todos los enlaces
    $queryParams = http_build_query([
        'qualityControl' => $qualityControl->id,
    ]);
@endphp

<x-app-layout>
    <div class="mb-6 ">
        <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
    </div>

    
    <div class="px-20 mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6 dark:text-white">
      
      
      
      
      {{-- 1) El acordeón ocupa 2/3 en pantallas grandes --}}
      <div class="lg:col-span-2 space-y-4 w-full">
  {{-- Botón agregar fase --}}
  
<div class="flex justify-end mb-4 ">
  @can('fase create')
    <a 
      href="{{ route('fases.create') . '?' . $queryParams }}" 
      class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3 bg-gray-100 dark:bg-gray-800"
    >
      <iconify-icon icon="ic:round-plus" class="text-lg mr-1"></iconify-icon>
      {{ __('Nueva Fase') }}
    </a>
  @endcan
</div>

   @foreach($qualityControl->fases as $fase)
      <div class="border rounded-md overflow-hidden">
        {{-- Cabecera --}}
        <div class="flex justify-between items-center px-4 py-2 bg-gray-100 dark:bg-gray-800">
          <button onclick="toggleCollapse('collapse-fase-{{ $fase->id }}')" class="flex-1 text-left dark:text-white font-semibold">
            {{ $fase->name }}
          </button>
          <div class="flex items-center space-x-2">
            @can('update', $fase)
              {{-- Editar fase --}}
              <a
                href="{{ route('fases.edit', $fase) . '?' . $queryParams }}"
                class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 dark:text-white rounded"
                title="Editar fase"
              >
                <iconify-icon icon="heroicons:pencil-square" class="text-lg" />
              </a>
            @endcan

            @can('delete', $fase)
              {{-- Eliminar fase --}}
              <form
                id="deleteFaseForm{{ $fase->id }}"
                action="{{ route('fases.destroy', $fase) }}"
                method="POST"
                class="inline"
              >
                @csrf
                @method('DELETE')
                <button
                  type="button"
                  onclick="sweetAlertDelete(event,'deleteFaseForm{{ $fase->id }}')"
                  class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 dark:text-white rounded"
                  title="Eliminar fase"
                >
                  <iconify-icon icon="heroicons:trash" class="text-lg" />
                </button>
              </form>
            @endcan

            {{-- Toggle collapse --}}
            <button onclick="toggleCollapse('collapse-fase-{{ $fase->id }}')" class="p-1">
              <svg class="h-5 w-5 transform transition-transform dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
          </div>
        </div>

        {{-- Contenido colapsable --}}
        <div id="collapse-fase-{{ $fase->id }}" class="p-4 bg-white dark:bg-slate-700 ">
          {{-- Botón agregar documento --}}
          <div class="flex justify-end mb-2">
            @can('create', App\Models\Document::class)
              <a
                href="{{ route('documents.create') . '?' . http_build_query(['fase' => $fase->id, 'qualityControl' => $qualityControl->id]) }}"
                class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3"
              >
                <iconify-icon icon="ic:round-plus" class="text-lg mr-1" />
                {{ __('Nuevo documento') }}
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
                    <span class="font-medium dark:text-white">{{ $doc->name }}</span>


                    @php
    // 1) Mapeo de colores según el key del status
    $statusColors = [
        'open'           => 'bg-blue-100 text-blue-800',
        'waiting_review' => 'bg-yellow-100 text-yellow-800',
        'accepted'       => 'bg-green-100 text-green-800',
        'rejected'       => 'bg-red-100 text-red-800',
    ];
    // 2) Obtenemos el key del status del documento (asumimos que $doc->status->key existe)
    $statusKey = $doc->status->key ?? null;
    // 3) Si no hay key o no está mapeado, usamos gris por defecto
    $badgeClasses = $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-white';
@endphp

<span class="ml-4 text-sm font-medium px-2 py-0.5 rounded {{ $badgeClasses }}">
  {{ $doc->status->label ?? 'Sin estado' }}
</span>



                  </div>
                  <div class="flex items-center space-x-2 dark:text-white">
                    @can('update', $doc)
                      <a
                        href="{{ route('documents.edit', $doc) . '?' . http_build_query(['fase' => $fase->id, 'qualityControl' => $qualityControl->id]) }}"
                        title="Editar documento"
                      >
                        <iconify-icon icon="heroicons:pencil-square " />
                      </a>
                    @endcan

                    @if($doc->url)
                      @can('document download')
                        <a href="{{ route('documents.download', $doc) }}" title="Descargar">
                          <iconify-icon icon="ic:baseline-download" />
                        </a>
                      @endcan
                    @endif

                    @can('waitingReview', $doc)
                      <a
                        href="{{ route('documents.waiting-review', ['document' => $doc->id]) . '?' . $queryParams . '&fase=' . $fase->id }}"
                        class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs"
                      >
                        Esperando revisión
                      </a>
                    @endcan

                    @can('markAsAccepted', $doc)
                      <a
                        href="{{ route('documents.mark-as-accept', $doc) . '?' . $queryParams . '&fase=' . $fase->id }}"
                        class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs"
                      >
                        Aceptar
                      </a>
                    @endcan

                    @can('markAsRejected', $doc)
                      <a
                        href="{{ route('documents.reject', ['document' => $doc->id]) . '?' . $queryParams . '&fase=' . $fase->id }}"
                        class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs"
                      >
                        Rechazar
                      </a>
                    @endcan

                    @can('delete', $doc)
                      <form
                        id="deleteDocForm{{ $doc->id }}"
                        action="{{ route('documents.destroy', $doc) }}"
                        method="POST"
                      >
                        @csrf
                        @method('DELETE')
                        <button
                          type="button"
                          onclick="sweetAlertDelete(event,'deleteDocForm{{ $doc->id }}')"
                          title="Eliminar documento"
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








        {{-- 2) Componente de pestañas al lado --}}
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm h-96 flex flex-col">
          {{-- Tabs --}}
          <nav class="flex border-b dark:border-slate-700">
            <button data-tab="status"
                    class="tab-button px-4 py-2 -mb-px border-b-2 font-medium border-blue-500 text-blue-600 dark:text-white">
              Estado
            </button>
            <button data-tab="comments"
        class="tab-button whitespace-nowrap px-4 py-2 -mb-px border-b-2 font-medium border-transparent text-gray-600 hover:text-gray-800 dark:text-white dark:hover:text-white">
  Comentarios ({{ $comments->count() }})
</button>

            <button data-tab="activity"
                    class="tab-button px-4 py-2 -mb-px border-b-2 font-medium border-transparent text-gray-600 hover:text-gray-800 dark:text-white dark:hover:text-white">
              Actividad
            </button>
          </nav>

          {{-- Panels --}}
          <div class="p-4 dark:text-white">
            {{-- ESTADO --}}
            <div id="panel-status" class="space-y-3 dark:text-white">
              @foreach([
                'open'           => ['label'=>'Abiertas','color'=>'bg-blue-500 text-blue-800 '],
                'waiting_review' => ['label'=>'En espera de revisión','color'=>'bg-yellow-500 text-yellow-800 '],
                'accepted'       => ['label'=>'Aceptadas','color'=>'  bg-green-500 text-green-800'],
                'rejected'       => ['label'=>'Rechazadas','color'=>'bg-red-500 text-red-800 '],
              ] as $key => $meta)
                <div class="flex items-center justify-between">
                  <span class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full {{ $meta['color'] }}"></span>
                    <span>{{ $meta['label'] }}</span>
                  </span>
                  <span class="font-semibold">{{ $statusCounts[$key] ?? 0 }}</span>
                </div>
              @endforeach
            </div>

             {{-- COMENTARIOS --}}
      <div id="panel-comments" class="hidden text-gray-600 dark:text-white h-full flex flex-col">
        <div class="flex-1 overflow-y-auto space-y-4 pr-2">
          @if($comments->isEmpty())
            <p class="text-sm italic text-center">{{ __('No hay comentarios para esta auditoría.') }}</p>
          @else
            @foreach($comments as $comment)
              <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                <div class="flex items-center space-x-2">
                  <img
                    src="{{ $comment->user->avatar ?: Avatar::create($comment->user->name)->setDimension(400)->setFontSize(240)->toBase64() }}"
                    class="h-8 w-8 rounded-full object-cover"
                    alt="Avatar de {{ $comment->user->name }}"
                  >
                  <div class="flex flex-col">
                    <span class="font-medium text-gray-800 dark:text-gray-200 text-sm">
                      {{ $comment->user->name }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                      {{ $comment->created_at }}
                    </span>
                  </div>
                </div>
                <p class="mt-1 text-gray-700 dark:text-white text-sm">
                  {{ $comment->comment }}
                </p>

                {{-- Si el comentario tiene respuestas anidadas (children), las mostramos aquí --}}
                @if($comment->comments->isNotEmpty())
                  <div class="mt-2 pl-8 space-y-2">
                    @foreach($comment->comments as $reply)
                      <div class="flex items-start space-x-2">
                        <img
                          src="{{ $reply->user->avatar ?: Avatar::create($reply->user->name)->setDimension(400)->setFontSize(240)->toBase64() }}"
                          class="h-6 w-6 rounded-full object-cover mt-1"
                          alt="Avatar de {{ $reply->user->name }}"
                        >
                        <div>
                          <div class="flex items-center space-x-1">
                            <span class="font-medium text-gray-700 dark:text-gray-200 text-sm">
                              {{ $reply->user->name }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                              {{ $reply->created_at }}
                            </span>
                          </div>
                          <p class="text-gray-700 dark:text-white text-sm">
                            {{ $reply->comment }}
                          </p>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
            @endforeach
          @endif
        </div>

        {{-- (Opcional) Paginación si usas ->paginate() en lugar de ->get() --}}
        {{-- <div class="mt-2">
          {{ $comments->links() }}
        </div> --}}
      </div>

           {{-- ACTIVIDAD --}}
{{-- ACTIVIDAD --}}
<div id="panel-activity" class="hidden text-gray-600 dark:text-white">
  {{-- Definimos un contenedor con altura fija y scroll interno --}}
  <div class="h-64 overflow-y-auto space-y-4">
    @if($histories->isEmpty())
      <p class="text-sm italic text-center">{{ __('No hay actividad registrada aún.') }}</p>
    @else
      @foreach($histories as $item)
        <div class="mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
          <div class="flex items-center space-x-2">
            <img
              src="{{ $item->user->avatar ?: Avatar::create($item->user->name)->setDimension(400)->setFontSize(240)->toBase64() }}"
              class="h-8 w-8 rounded-full object-cover"
              alt="Avatar de {{ $item->user->name }}"
            >
            <div class="flex flex-col">
              <span class="font-medium text-gray-800 dark:text-gray-200 text-sm">
                {{ $item->user->name }}
              </span>
              <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ $item->created_at }}
              </span>
            </div>
          </div>
          <p class="mt-1 text-gray-700 dark:text-white text-sm">
            {{ $item->description }}
          </p>
        </div>
      @endforeach
    @endif
  </div>
</div>


          </div>
        </div>
    </div>

    @push('scripts')
    <script>
      // Función del acordeón
      function toggleCollapse(id) {
        document.getElementById(id)?.classList.toggle('hidden');
      }

      // Confirmación de borrado
      function sweetAlertDelete(event, formId) {
        event.preventDefault();
        const form = document.getElementById(formId);
        Swal.fire({
          title: '¿Estás seguro?',
          icon: 'question',
          showDenyButton: true,
          confirmButtonText: 'Eliminar',
          denyButtonText: 'Cancelar',
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      }

      // Lógica de tabs
      document.querySelectorAll('.tab-button').forEach(btn => {
        btn.addEventListener('click', () => {
          const tab = btn.dataset.tab;
          // actualizar botones
          document.querySelectorAll('.tab-button').forEach(b => {
            b.classList.toggle('border-blue-500', b === btn);
            b.classList.toggle('text-blue-600', b === btn);
            b.classList.toggle('border-transparent', b !== btn);
            b.classList.toggle('text-gray-600', b !== btn);
          });
          // ocultar/mostrar paneles
          ['status','comments','activity'].forEach(name => {
            document.getElementById('panel-'+name)
                    .classList.toggle('hidden', name !== tab);
          });
        });
      });
    </script>
    @endpush
</x-app-layout>
