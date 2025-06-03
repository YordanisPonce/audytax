@php
    // Construimos la query string para pasarla a todos los enlaces
    $queryParams = http_build_query([
        'qualityControl' => $qualityControl->id,
    ]);
@endphp

<x-app-layout>
    <div class="mb-6">
        <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
    </div>

    
    {{-- ===================================================================
    CONTENIDO PRINCIPAL: Fases → Documentos
    =================================================================== --}}
    <div class="px-15 mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6 dark:text-white">
        {{-- 1) El acordeón ocupa 2/3 en pantallas grandes --}}
        <div class="lg:col-span-2 space-y-4 w-full">
            {{-- ===================================================================
                BUSCADOR GENERAL + BOTÓN COLLAPSE/EXPAND ALL
                =================================================================== --}}
            <div class="px-5 mx-auto flex justify-between items-center mb-6">
                {{-- Contenedor del input con borde neutro, que cambia a azul en focus --}}
                <div class="flex items-center border border-gray-300 rounded-md overflow-hidden w-full max-w-2xl
                            focus-within:border-blue-500 transition-colors duration-200">
                    <div class="px-3 text-gray-500">
                        <iconify-icon icon="heroicons-outline:search"></iconify-icon>
                    </div>
                    <input
                        type="text"
                        id="generalSearch"
                        placeholder="Buscar Documentos..."
                        class="w-full py-2 px-2 text-sm outline-none focus:ring-0 border-0 bg-transparent"
                        oninput="filterDocuments()"
                    />
                </div>
        
                {{-- Botón que alterna entre "Collapse all groups" y "Expand all groups" --}}
                <button
                    id="toggleCollapseBtn"
                    onclick="toggleCollapseAllFases()"
                    class="ml-4 text-sm text-blue-600 hover:underline focus:outline-none"
                >
                    Colapsar grupos
                </button>
            </div>
            
            {{-- Botón "Nueva Fase" --}}
            <div class="flex justify-start mb-4">
                @can('fase create')
                    <a
                        href="{{ route('fases.create') . '?' . $queryParams }}"
                        class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3 bg-gray-100 dark:bg-gray-800"
                    >
                        <iconify-icon icon="ic:round-plus" class="text-lg mr-1"></iconify-icon>
                        {{ __('Nuevo Grupo') }}
                    </a>
                @endcan
            </div>

            {{-- Iteramos sobre cada fase --}}
            @foreach($qualityControl->fases as $fase)
                {{-- Cada fase envuelta en un DIV con atributo data-fase --}}
                <div class="border rounded-md overflow-hidden" data-fase="fase-{{ $fase->id }}">
                    {{-- Cabecera de la fase --}}
                    <div class="flex justify-between items-center px-4 py-2 bg-gray-100 dark:bg-gray-800">
                        <button
                            onclick="toggleCollapse('collapse-fase-{{ $fase->id }}')"
                            class="flex-1 text-left dark:text-white font-semibold"
                        >
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

                            {{-- Ícono para colapsar/expandir individual --}}
                            <button onclick="toggleCollapse('collapse-fase-{{ $fase->id }}')" class="p-1">
                                <svg
                                    class="h-5 w-5 transform transition-transform dark:text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                    </div>





                    {{-- Contenido colapsable de la fase (documentos) --}}
                    <div
                        id="collapse-fase-{{ $fase->id }}"
                        class="p-4 bg-white dark:bg-slate-700"
                    >
                       {{-- Dentro de la iteración sobre fases, en cada “collapse-fase-{{ $fase->id }}” --}}
@if($fase->documents->isEmpty())
    <p class="text-sm text-gray-500 dark:text-gray-400">
        No hay documentos en esta fase.
    </p>
@else
    <ul class="space-y-2">
        @foreach($fase->documents as $doc)
            {{-- Cada documento con data-name para filtrado --}}
            <li
                class="flex justify-between items-center bg-gray-50 dark:bg-slate-800 p-2 rounded"
                data-name="{{ strtolower($doc->name) }}"
            >
                <div class="flex-1 flex items-center space-x-2 overflow-hidden">
                    {{-- 1) Ícono de documento + Nombre --}}
                    <iconify-icon icon="heroicons-outline:document-text" class="text-lg text-slate-600 dark:text-slate-300"></iconify-icon>
                    <div class="truncate">
                        <span class="font-medium text-slate-800 dark:text-slate-200 truncate">
                            {{ $doc->name }}
                        </span>
                        @isset($doc->original_name)
                            <small class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                ({{ $doc->original_name }})
                            </small>
                        @endisset
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    @php $key = $doc->status->key; @endphp

                    {{-- 2) Badges con íconos según el estado --}}
                    @if($key === 'open')
                        {{-- Badge azul “Abierto” --}}
                        <span class="inline-flex items-center space-x-1 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 px-2 py-0.5 rounded-full text-xs font-medium">
                            <iconify-icon icon="heroicons-outline:clock" class="text-sm"></iconify-icon>
                            <span>Abierto</span>
                        </span>

                    @elseif($key === 'waiting_review')
                        {{-- Badge amarilla “En revisión” --}}
                        <span class="inline-flex items-center space-x-1 bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100 px-2 py-0.5 rounded-full text-xs font-medium">
                            <iconify-icon icon="heroicons-outline:refresh" class="animate-spin text-sm text-yellow-600"></iconify-icon>
                            <span>En revisión</span>
                        </span>

                    @elseif($key === 'accepted')
                        {{-- Badge verde “Aceptado” --}}
                        <span class="inline-flex items-center space-x-1 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 px-2 py-0.5 rounded-full text-xs font-medium">
                            <iconify-icon icon="heroicons-solid:check-circle" class="text-sm"></iconify-icon>
                            <span>Aceptado</span>
                        </span>

                    @elseif($key === 'rejected')
                        {{-- Badge roja “Rechazado” --}}
                        <span class="inline-flex items-center space-x-1 bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100 px-2 py-0.5 rounded-full text-xs font-medium">
                            <iconify-icon icon="heroicons-solid:x-circle" class="text-sm"></iconify-icon>
                            <span>Rechazado</span>
                        </span>
                    @endif

                    {{-- 3) Acciones permitidas sobre el documento --}}
                    @can('update', $doc)
                        {{-- Editar documento --}}
                        <a
                            href="{{ route('documents.edit', $doc) . '?' . http_build_query(['fase' => $fase->id, 'qualityControl' => $qualityControl->id]) }}"
                            class="p-1 rounded hover:bg-gray-200 dark:hover:bg-slate-700 dark:text-white transition"
                            title="Editar documento"
                        >
                            <iconify-icon icon="heroicons:pencil-square" class="text-lg"></iconify-icon>
                        </a>
                    @endcan

                    @if($doc->url)
                        @can('document download')
                            {{-- Descargar --}}
                            <a
                                href="{{ route('documents.download', $doc) }}"
                                class="p-1 rounded hover:bg-gray-200 dark:hover:bg-slate-700 dark:text-white transition"
                                title="Descargar"
                            >
                                <iconify-icon icon="heroicons-outline:download" class="text-lg"></iconify-icon>
                            </a>
                        @endcan
                    @endif

                    @can('waitingReview', $doc)
                        {{-- Marcar como "Esperando revisión" --}}
                        <a
                            href="{{ route('documents.waiting-review', ['document' => $doc->id]) . '?' . http_build_query(['qualityControl' => $qualityControl->id]) . '&fase=' . $fase->id }}"
                            class="px-2 py-0.5 bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100 rounded text-xs font-medium"
                        >
                            Esperando revisión
                        </a>
                    @endcan

                    @can('markAsAccepted', $doc)
                        {{-- Marcar como Aceptar --}}
                        <a
                            href="{{ route('documents.mark-as-accept', $doc) . '?' . http_build_query(['qualityControl' => $qualityControl->id]) . '&fase=' . $fase->id }}"
                            class="px-2 py-0.5 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 rounded text-xs font-medium"
                        >
                            Aceptar
                        </a>
                    @endcan

                    @can('markAsRejected', $doc)
                        {{-- Marcar como Rechazar --}}
                        <a
                            href="{{ route('documents.reject', ['document' => $doc->id]) . '?' . http_build_query(['qualityControl' => $qualityControl->id]) . '&fase=' . $fase->id }}"
                            class="px-2 py-0.5 bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100 rounded text-xs font-medium"
                        >
                            Rechazar
                        </a>
                    @endcan

                    @can('delete', $doc)
                        {{-- Eliminar documento --}}
                        <form
                            id="deleteDocForm{{ $doc->id }}"
                            action="{{ route('documents.destroy', $doc) }}"
                            method="POST"
                            class="inline"
                        >
                            @csrf
                            @method('DELETE')
                            <button
                                type="button"
                                onclick="sweetAlertDelete(event,'deleteDocForm{{ $doc->id }}')"
                                class="p-1 rounded hover:bg-gray-200 dark:hover:bg-slate-700 dark:text-white transition"
                                title="Eliminar documento"
                            >
                                <iconify-icon icon="heroicons:trash" class="text-lg"></iconify-icon>
                            </button>
                        </form>
                    @endcan
                </div>
            </li>
        @endforeach
    </ul>

    {{-- Botón “Nuevo documento” al final de la lista --}}
    <div class="flex justify-start mt-4">
        @can('create', App\Models\Document::class)
            <a
                href="{{ route('documents.create') . '?' . http_build_query(['fase' => $fase->id, 'qualityControl' => $qualityControl->id]) }}"
                class="btn inline-flex justify-center btn-dark rounded-[25px] items-center !p-2 !px-3"
            >
                <iconify-icon icon="ic:round-plus" class="text-lg mr-1"></iconify-icon>
            </a>
        @endcan
    </div>
@endif

                    </div>
                </div>
            @endforeach
        </div>

        {{-- ===================================================================
            2) Pestañas: Estado / Comentarios / Actividad
            =================================================================== --}}
        <div class="bg-white dark:bg-slate-800 rounded-md shadow-sm h-96 flex flex-col">
            {{-- Tabs --}}
            <nav class="flex border-b dark:border-slate-700">
                <button
                    data-tab="status"
                    class="tab-button px-4 py-2 -mb-px border-b-2 font-medium border-blue-500 text-blue-600 dark:text-white"
                >
                    Estado
                </button>
                <button
                    data-tab="comments"
                    class="tab-button whitespace-nowrap px-4 py-2 -mb-px border-b-2 font-medium border-transparent text-gray-600 hover:text-gray-800 dark:text-white dark:hover:text-white"
                >
                    Comentarios ({{ $comments->count() }})
                </button>
                <button
                    data-tab="activity"
                    class="tab-button px-4 py-2 -mb-px border-b-2 font-medium border-transparent text-gray-600 hover:text-gray-800 dark:text-white dark:hover:text-white"
                >
                    Actividad
                </button>
            </nav>

            {{-- Panels --}}
            <div class="p-4 dark:text-white overflow-y-auto flex-1">
                {{-- ESTADO --}}
<div id="panel-status" class="dark:text-white space-y-3">

    {{-- ------------------------------------------------------
         1) Barrita de progreso con los cuatro colores, en orden:
            - Verde (“accepted”)
            - Rojo (“rejected”)
            - Amarillo (“waiting_review”)
            - Azul (“open”)
         Cada segmento tendrá ancho proporcional al porcentaje
         que ocupa ese estado sobre el total de documentos.
       ------------------------------------------------------ --}}
    @php
        // 1) Conteos individuales:
        $acceptedCount = $statusCounts['accepted'] ?? 0;
        $rejectedCount = $statusCounts['rejected'] ?? 0;
        $waitingCount  = $statusCounts['waiting_review'] ?? 0;
        $openCount     = $statusCounts['open'] ?? 0;

        // 2) Usamos EXACTAMENTE el total que vino del controlador:
        //    (Si por alguna razón no existe o es 0, forzamos a 1 para no dividir por cero)
        $total = isset($totalDocuments) && $totalDocuments > 0
                 ? $totalDocuments
                 : 1;

        // 3) Porcentajes “raw” (sin redondear) para los tres primeros:
        $rawAcc  = ($acceptedCount  * 100) / $total;
        $rawRej  = ($rejectedCount  * 100) / $total;
        $rawWait = ($waitingCount   * 100) / $total;
        // El porcentaje “Open” lo calculamos al final como “100 − (suma de los tres anteriores)”.

        // 4) Redondeamos a 1 decimal los tres primeros:
        $pctAccepted = round($rawAcc, 1);
        $pctRejected = round($rawRej, 1);
        $pctWaiting  = round($rawWait, 1);

        // 5) El porcentaje “Open” es el resto hasta 100 (evitando quedarnos fuera de rango):
        $pctOpen = 100.0 - ($pctAccepted + $pctRejected + $pctWaiting);

        // 6) Si por redondeo queda negativo o mayor que 100, lo corregimos:
        if ($pctOpen < 0) {
            $pctOpen = 0.0;
        } elseif ($pctOpen > 100) {
            $pctOpen = 100.0;
        }
    @endphp

    {{-- La barra propiamente dicha --}}
    {{-- <div class="h-2 w-full bg-gray-200 dark:bg-slate-700 rounded overflow-hidden">
       
        <div
            class="h-full bg-green-500 inline-block"
            style="width: {{ $pctAccepted }}%;"
        ></div>

       
        <div
            class="h-full bg-red-500 inline-block"
            style="width: {{ $pctRejected }}%;"
        ></div>

       
        <div
            class="h-full bg-yellow-500 inline-block"
            style="width: {{ $pctWaiting }}%;"
        ></div>

       
        <div
            class="h-full bg-blue-500 inline-block"
            style="width: {{ $pctOpen }}%;"
        ></div>
    </div> --}}

    {{-- Leyenda de porcentajes debajo --}}
    <div class="flex text-xs text-gray-600 dark:text-gray-300 mt-1 space-x-4">
        <span class="flex items-center space-x-1">
            <span class="block w-3 h-3 bg-green-500 rounded-full"></span>
            <span>Aceptadas: {{ $pctAccepted }}%</span>
        </span>
        <span class="flex items-center space-x-1">
            <span class="block w-3 h-3 bg-red-500 rounded-full"></span>
            <span>Rechazadas: {{ $pctRejected }}%</span>
        </span>
        <span class="flex items-center space-x-1">
            <span class="block w-3 h-3 bg-yellow-500 rounded-full"></span>
            <span>En revisión: {{ $pctWaiting }}%</span>
        </span>
        <span class="flex items-center space-x-1">
            <span class="block w-3 h-3 bg-blue-500 rounded-full"></span>
            <span>Abiertas: {{ $pctOpen }}%</span>
        </span>
    </div>

    {{-- ------------------------------------------------------
         2) Ahora la lista normal de estados y conteos:
       ------------------------------------------------------ --}}
    @foreach([
        'open'           => ['label'=>'Abiertas','color'=>'bg-blue-500 text-blue-800'],
        'waiting_review' => ['label'=>'En espera de revisión','color'=>'bg-yellow-500 text-yellow-800'],
        'accepted'       => ['label'=>'Aceptadas','color'=>'bg-green-500 text-green-800'],
        'rejected'       => ['label'=>'Rechazadas','color'=>'bg-red-500 text-red-800'],
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
                {{-- COMENTARIOS --}}
<div id="panel-comments" class="hidden text-gray-600 dark:text-white h-full flex flex-col">

    
    
    {{-- 2) Lista de comentarios existentes --}}
    <div class="flex-1 overflow-y-auto space-y-4 pr-2">
        @if($comments->isEmpty())
            <p class="text-sm italic text-center">
                {{ __('No hay comentarios para esta auditoría.') }}
            </p>
            @else
            @foreach($comments as $comment)
            <div class="border-b border-gray-200 dark:border-gray-700 pb-3">
                    <div class="flex items-center space-x-2">
                        <img
                            src="{{ $comment->user->avatar
                                     ?: Avatar::create($comment->user->name)
                                     ->setDimension(400)
                                        ->setFontSize(240)
                                        ->toBase64() }}"
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

                    @if($comment->comments->isNotEmpty())
                        <div class="mt-2 pl-8 space-y-2">
                            @foreach($comment->comments as $reply)
                                <div class="flex items-start space-x-2">
                                    <img
                                        src="{{ $reply->user->avatar
                                                 ?: Avatar::create($reply->user->name)
                                                    ->setDimension(400)
                                                    ->setFontSize(240)
                                                    ->toBase64() }}"
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

    {{-- 1) Formulario de nuevo comentario (solo si @can('createComment', $qualityControl)) --}}
    {{-- Formulario para que el Admin (o quien esté autorizado) deje un comentario en este QC --}}
    @can('createComment', $qualityControl)
    <form
        action="{{ route('qualityControls.comments.store', $qualityControl->id) }}"
        method="POST"
        class="mb-4"
    >
        @csrf
        <textarea
            name="comment"
            rows="3"
            class="w-full border rounded px-2 py-1 dark:bg-slate-700"
            placeholder="Escribe aquí tu comentario..."
            required
        ></textarea>
        <x-input-error :messages="$errors->get('comment')" class="mt-1 text-sm text-red-600" />
    
        <button
            type="submit"
            class="mt-2 inline-flex items-center px-4 py-2 bg-blue-600  text-white rounded hover:bg-blue-700"
        >
            Enviar comentario
        </button>
    </form>
    @endcan
</div>

                {{-- ACTIVIDAD --}}
                <div id="panel-activity" class="hidden text-gray-600 dark:text-white h-full flex flex-col">
                    <div class="h-64 overflow-y-auto space-y-4">
                        @if($histories->isEmpty())
                            <p class="text-sm italic text-center">
                                {{ __('No hay actividad registrada aún.') }}
                            </p>
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

    {{-- ===================================================================
        SCRIPTS NECESARIOS
        =================================================================== --}}
    @push('scripts')
    <script>
        // Función para colapsar/expandir un elemento por su id
        function toggleCollapse(id) {
            document.getElementById(id)?.classList.toggle('hidden');
        }

        // Variable que indica si todas las fases están colapsadas (true) o expandidas (false)
        let allCollapsed = false;

        // Alterna entre colapsar todas o expandir todas las fases
        function toggleCollapseAllFases() {
            // Recuperamos todos los divs de fase (data-fase)
            const fases = document.querySelectorAll('[data-fase]');

            if (!allCollapsed) {
                // Si NO estaban todas colapsadas, las colapsamos
                fases.forEach(group => {
                    const collapseDiv = group.querySelector('[id^="collapse-fase-"]');
                    collapseDiv?.classList.add('hidden');
                });
                // Cambiamos texto del botón y estado
                document.getElementById('toggleCollapseBtn').textContent = 'Expandir grupos';
                allCollapsed = true;
            } else {
                // Si YA estaban colapsadas, expandimos todas
                fases.forEach(group => {
                    const collapseDiv = group.querySelector('[id^="collapse-fase-"]');
                    collapseDiv?.classList.remove('hidden');
                });
                document.getElementById('toggleCollapseBtn').textContent = 'Colapsar grupos';
                allCollapsed = false;
            }
        }

        // Filtrado general: muestra/oculta fases y documentos según coincidencia
        function filterDocuments() {
            const query = document.getElementById('generalSearch').value.toLowerCase();

            // Recorremos cada contenedor de fase
            document.querySelectorAll('[data-fase]').forEach(group => {
                let hasMatch = false;

                // Dentro de cada fase, buscamos coincidencias en cada <li data-name="...">
                group.querySelectorAll('[data-name]').forEach(item => {
                    const name = item.dataset.name; // ya en minúsculas
                    const match = name.includes(query);

                    // Ocultamos o mostramos el <li> según match
                    item.classList.toggle('hidden', !match);
                    if (match) hasMatch = true;
                });

                // Si la fase NO tiene ningún documento visible, ocultamos esa fase entera
                // (ocultamos el contenedor <div data-fase="...">)
                group.classList.toggle('hidden', !hasMatch);

                // Si hay texto de búsqueda, aseguramos que la fase esté expandida
                if (query.length > 0 && hasMatch) {
                    const collapseDiv = group.querySelector('[id^="collapse-fase-"]');
                    collapseDiv?.classList.remove('hidden');
                }
            });
        }

        // Confirmación de borrado (SweetAlert2)
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

        // Lógica de tabs (estado / comentarios / actividad)
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tab;
                // Actualizar estilo de botones
                document.querySelectorAll('.tab-button').forEach(b => {
                    b.classList.toggle('border-blue-500', b === btn);
                    b.classList.toggle('text-blue-600', b === btn);
                    b.classList.toggle('border-transparent', b !== btn);
                    b.classList.toggle('text-gray-600', b !== btn);
                });
                // Mostrar/ocultar paneles
                ['status', 'comments', 'activity'].forEach(name => {
                    document.getElementById('panel-' + name)
                            .classList.toggle('hidden', name !== tab);
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
