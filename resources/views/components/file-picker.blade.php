{{-- resources/views/components/file-picker.blade.php --}}
@props(['document', 'fileId', 'fileName'])

<div class="flex w-full mt-4 items-center justify-between gap-2 border-b pb-2 md:pb-4 file-picker-container">
    {{-- 1) Icono + Nombre del documento (y original_name si existe) --}}
    <div class="flex-1 flex items-center space-x-2 overflow-hidden">
        <iconify-icon icon="heroicons-outline:document-text" class="text-lg text-slate-600 dark:text-slate-300"></iconify-icon>
        <div class="truncate">
            <span class="font-medium text-slate-800 dark:text-slate-200 truncate">
                {{ $document->name ?? 'No definido' }}
            </span>
            @isset($document->original_name)
                <small class="text-sm text-slate-500 dark:text-slate-400 truncate">
                    ({{ $document->original_name }})
                </small>
            @endisset
        </div>
    </div>

    {{-- 2) Zona según estado del documento --}}
    <div class="flex items-center space-x-2">
        @php $key = $document->status->key; @endphp

        {{-- — 1) ESTADO “OPEN” (Abierto) — --}}
        @if($key === 'open')
            {{-- Badge azul “Abierto” --}}
            <span class="inline-flex items-center space-x-1 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100 px-2 py-1 rounded-full text-sm font-medium">
                <iconify-icon icon="heroicons-outline:clock" class="text-base"></iconify-icon>
                <span>Abierto</span>
            </span>

            @hasrole('client')
                {{-- Cliente puede subir un nuevo archivo --}}
                <label for="{{ $fileId }}"
                       class="cursor-pointer p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <iconify-icon icon="heroicons-outline:cloud-upload" class="text-2xl text-blue-500"></iconify-icon>
                    <input type="file"
                           accept=".jpg,.png,.xlsx,.xls,.csv,.doc,.docx,.ppt,.pptx,.pdf"
                           name="{{ $fileName }}"
                           id="{{ $fileId }}"
                           class="hidden"
                           onchange="handleInputChange(event)">
                </label>

                {{-- Cancelar subida --}}
                <button type="button"
                        class="cancel-button hidden p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                        onclick="handleDeleteFile(event)">
                    <iconify-icon icon="heroicons-outline:trash" class="text-2xl text-red-500"></iconify-icon>
                </button>
            @endhasrole

        {{-- — 2) ESTADO “WAITING_REVIEW” (En revisión) — --}}
        @elseif($key === 'waiting_review')
            @hasrole('client')
                {{-- Badge amarilla “En revisión” --}}
                <span class="inline-flex items-center space-x-1 bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100 px-2 py-1 rounded-full text-sm font-medium">
                    <iconify-icon icon="heroicons-outline:refresh" class="animate-spin text-base"></iconify-icon>
                    <span>En revisión</span>
                </span>
            @else


                {{-- Admin/Consultor ve botones “Aceptar” y “Rechazar” --}}
             {{-- Formulario de Aceptar --}}
<form 
    action="{{ route('documents.mark-as-accept', ['document' => $document]) }}"
    method="GET"
    class="inline confirm-form"
    data-action="accept"
>
    @csrf
    <a href="{{ route('documents.mark-as-accept', ['document' => $document]) }}"
                   class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                   title="Aceptar">
                    <iconify-icon icon="heroicons-solid:check-circle" class="text-2xl text-green-500"></iconify-icon>
                </a>
</form>

<form 
    action="{{ route('documents.reject', ['document' => $document]) }}"
    method="GET"
    class="inline confirm-form"
    data-action="reject"
>
    @csrf
    <button 
        type="submit"
        class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
        title="Rechazar"
        style="background: transparent; border: none;"
    >
        <iconify-icon icon="heroicons-solid:x-circle" class="text-2xl text-red-500"></iconify-icon>
    </button>
</form>



            @endhasrole



        {{-- — 3) ESTADO “ACCEPTED” (Aceptado) — --}}
        @elseif($key === 'accepted')
            <span class="inline-flex items-center space-x-1 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100 px-2 py-1 rounded-full text-sm font-medium">
                <iconify-icon icon="heroicons-solid:check-circle" class="text-base"></iconify-icon>
                <span>Aceptado</span>
            </span>

        {{-- — 4) ESTADO “REJECTED” (Rechazado) — --}}
        @elseif($key === 'rejected')
            <span class="inline-flex items-center space-x-1 bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100 px-2 py-1 rounded-full text-sm font-medium">
                <iconify-icon icon="heroicons-solid:x-circle" class="text-base"></iconify-icon>
                <span>Rechazado</span>
            </span>

            @hasrole('client')
                {{-- Si está rechazado, el cliente puede volver a subir --}}
                <label for="{{ $fileId }}"
                       class="cursor-pointer p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <iconify-icon icon="heroicons-outline:cloud-upload" class="text-2xl text-blue-500"></iconify-icon>
                    <input type="file"
                           accept=".jpg,.png,.xlsx,.xls,.csv,.doc,.docx,.ppt,.pptx,.pdf"
                           name="{{ $fileName }}"
                           id="{{ $fileId }}"
                           class="hidden"
                           onchange="handleInputChange(event)">
                </label>

                {{-- Cancelar subida --}}
                <button type="button"
                        class="cancel-button hidden p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                        onclick="handleDeleteFile(event)">
                    <iconify-icon icon="heroicons-outline:trash" class="text-2xl text-red-500"></iconify-icon>
                </button>
            @endhasrole
        @endif
    </div>

    {{-- 3) Descargar (si no está “open”) --}}
    @if($key !== 'open')
        <a href="{{ route('documents.download', ['document' => $document]) }}"
           class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition"
           title="Descargar">
            <iconify-icon icon="heroicons-outline:download" class="text-xl text-slate-600 dark:text-slate-300"></iconify-icon>
        </a>
    @endif
</div>

@push('scripts')
<script>
    function handleInputChange(event) {
        const input = event.target;
        const container = input.closest('.file-picker-container');
        const uploadLabel = container.querySelector('label[for="' + input.id + '"]');
        const cancelBtn = container.querySelector('.cancel-button');

        // Oculta el label de upload y muestra el botón de cancelar
        if (uploadLabel) uploadLabel.classList.add('hidden');
        if (cancelBtn) cancelBtn.classList.remove('hidden');
    }

    function handleDeleteFile(event) {
        const cancelBtn = event.currentTarget;
        const container = cancelBtn.closest('.file-picker-container');
        const oldInput = container.querySelector('input[type="file"]');
        const uploadLabel = container.querySelector('label[for="' + oldInput.id + '"]');

        // Restaurar el input de file (para que quede vacío)
        const newInput = document.createElement('input');
        newInput.type = 'file';
        newInput.name = oldInput.name;
        newInput.id   = oldInput.id;
        newInput.accept = oldInput.accept;
        newInput.classList.add('hidden');
        newInput.addEventListener('change', handleInputChange);
        oldInput.parentNode.replaceChild(newInput, oldInput);

        // Oculta el botón de cancelar, muestra otra vez el label de upload
        cancelBtn.classList.add('hidden');
        if (uploadLabel) uploadLabel.classList.remove('hidden');
    }

    // Opcional: inicializar tooltips con Tippy.js (si lo usas)
    function initTooltipPicker() {
        if (window.innerWidth > 768 && typeof tippy !== 'undefined') {
            tippy(".file-picker-container [title]", {
                placement: "top",
                allowHTML: true,
                maxWidth: 200,
                appendTo: document.body,
                popperOptions: {
                    modifiers: [
                        { name: 'offset', options: { offset: [0, 10] } },
                        { name: 'preventOverflow', options: { padding: 10 } },
                        { name: 'computeStyles', options: { gpuAcceleration: false } },
                    ],
                },
            });
        }
    }

document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.confirm-form');

    forms.forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const action = form.getAttribute('data-action');
            let title = '';
            let confirmButtonText = '';

            if (action === 'accept') {
                title = '¿Está seguro que desea aceptar este documento?';
                confirmButtonText = 'Aceptar';
            } else if (action === 'reject') {
                title = '¿Está seguro que desea rechazar este documento?';
                confirmButtonText = 'Rechazar';
            } else {
                title = '¿Está seguro de realizar esta acción?';
                confirmButtonText = 'Sí';
            }

            Swal.fire({
                title: title,
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: confirmButtonText,
                denyButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});



    initTooltipPicker();
</script>
@endpush
