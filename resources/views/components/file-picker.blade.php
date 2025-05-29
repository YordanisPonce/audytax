@props(['document', 'fileId', 'fileName'])

<div class="flex w-full mt-4 items-center justify-between gap-2 border-b pb-2 md:pb-4 file-picker-container gap-2">
    <p class="break-all flex gap-2 max-md:flex-wrap grow truncate">
        <span class="truncate">
            {{ $document->name ?? 'No definido' }}&nbsp;@isset($document->original_name)
                ({{ $document->original_name }})
            @endisset
        </span>
        <small class="grow truncate">
            <a class="text-blue-500 hover:underline truncate w-1/2 file-name" target="_blank" href="">

            </a>
        </small>
    </p>
    @if ($document->isWaiting())
        @hasrole('client')
            <label for="{{ $fileId }}"
                class="ml-auto cursor-pointer active:scale-90 transition-all duration-100 input-area">
                <iconify-icon class="text-2xl flex-0" icon="basil:upload-outline" />
                <input type="file" accept=".jpg, .png, .xlsx, .xls, .csv, .doc, .docx, .ppt, .pptx, .pdf"
                    name="{{ $fileName }}" id="{{ $fileId }}" class="hidden" onchange="handleInputChange(event)">
            </label>
            <a class="cursor-pointer cancel-button hidden" onclick="handleDeleteFile(event)">
                <iconify-icon class="text-2xl flex-0" icon="mdi:cancel-circle-outline" />
            </a>
        @else
            <p class="onTop"></p>
            <iconify-icon icon="ic:sharp-pending-actions"></iconify-icon>
        @endhasrole
    @elseif ($document->isProcessing())
        @hasrole('client')
            <x-processing :className="'h-5 w-5'" :document="$document" />
        @else
            <a @class(['action-btn', 'text-success-500' => $document->isComplete()]) href="{{ route('documents.mark-as-complete', ['document' => $document]) }}">
                <iconify-icon icon="material-symbols:check"></iconify-icon>
            </a>
            <a @class(['action-btn text-danger-500']) href="{{ route('documents.cancel-document', ['document' => $document]) }}">
                <iconify-icon icon="mdi:cancel-bold"></iconify-icon>
            </a>
        @endhasrole
    @elseif ($document->isComplete())
        <span class="badge bg-success-500 text-white capitalize inline-flex items-center">Aprobado</span>
    @endif
    @if (!$document->isWaiting())
        <a class="action-btn" href="{{ route('documents.download', ['document' => $document]) }}">
            <iconify-icon icon="ic:baseline-download"></iconify-icon>
        </a>
    @endif
</div>

@push('scripts')
    <script>
        function handleInputChange(event) {
            event = event || window.event;
            const {
                target
            } = event;
            const a = target.closest('div.file-picker-container').querySelector('.file-name');
            const label = target.closest('label');
            label && label.classList.add('hidden')
            const delButton = label.closest('div').querySelector('.cancel-button')
            delButton && delButton.classList.remove('hidden')
            if (a) {
                a.href = URL.createObjectURL(target.files[0]);
                a.textContent = `(${target.files[0]?.name})`
            }
        }

        function handleDeleteFile(event) {
            let newInputElement = document.createElement('input');
            const input = event.target.closest('div').querySelector('input');

            newInputElement.type = 'file';
            newInputElement.name = input?.name; // Establece el nombre del campo en el formulario
            newInputElement.id = input?.id;
            newInputElement.classList = 'd-none';
            newInputElement.addEventListener('change', handleInputChange);

            input.value = "";
            const parent = event.target.closest('div');
            const inputArea = event.target.closest('div').querySelector('.input-area')
            inputArea.classList.remove('hidden')
            event.currentTarget.classList.add('hidden')
            input.parentNode.replaceChild(newInputElement, input);
            const a = event.target.closest('div.file-picker-container').querySelector('.file-name');
            if (a) {
                a.href = "";
                a.textContent = ``
            }
        }


        function initTooltipPicker() {
            if (window.innerWidth > 768) {
                // Tooltip and Popover
                tippy(".onTop", {
                    content: "Sin descripci&oacute;n disponible",
                    placement: "top",
                    allowHTML: true,
                    maxWidth: 200,
                    // Estilos CSS personalizados
                    appendTo: document.body,
                    popperOptions: {
                        modifiers: [{
                                name: 'offset',
                                options: {
                                    offset: [0, 10],
                                },
                            },
                            {
                                name: 'preventOverflow',
                                options: {
                                    padding: 10,
                                },
                            },
                            {
                                name: 'computeStyles',
                                options: {
                                    gpuAcceleration: false,
                                },
                            },
                        ],
                    },
                    onCreate(instance) {
                        // Aplica estilos adicionales al contenido del tooltip
                        const tooltipContent = instance.popper.querySelector('.tippy-content');
                        tooltipContent.style.whiteSpace = 'pre-wrap';
                        tooltipContent.style.wordWrap = 'break-word';
                    },
                });
            }
        }

        initTooltipPicker();
    </script>
@endpush
