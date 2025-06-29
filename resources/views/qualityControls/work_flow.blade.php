@php
    $route = \Illuminate\Support\Facades\Route::current();
    $id = $route->parameter('id');
    $faseId = $route->parameter('fase');
@endphp

<x-app-layout>
    <div class="pb-20">
        <div class=" mb-6 md:mb-2">
            {{-- Breadcrumb start --}}
            <x-breadcrumb :subtitle="'Completado: ' . $qualityControl->getFinishPercent() . '%'" :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />

        </div>
        <div class="flex md:flex-col max-md:gap-2 px-20">
            {{-- Alert start --}}
        @if (session('message'))
            <x-alert :message="session('message')" :type="'success'" />
        @endif

            <div @class([
                'flex max-md:flex-col relative gap-8 items-center md:overflow-hidden max-md:w-12 h-full',
                'justify-between' => count($qualityControl->fases) > 1,
                'justify-center' => count($qualityControl->fases) <= 2,
            ]) class="">
                @foreach ($qualityControl->fases as $item)
                    <div @class([
                        'flex items-center flex-col md:mt-4 relative',
                        'max-md:mt-4' => $loop->iteration !== 1,
                        'active' => $fase->id == $item->id,
                    ])>
                        <a href="{{ route('qualityControls.details', ['qualityControl' => $qualityControl, 'fase' => $item->id]) }}"
                            data-tippy-content="Estado: {{ $item->getFinishPercent() }}%" data-tippy-theme="black"
                            @class([
                                'toolTip onBottom h-11 w-11 md:h-16 md:w-16 rounded-full grid place-items-center scale-75 hover:scale-[.65] cursor-pointer transition-all duration-200 ease-in-out',
                                'bg-blue-300' => $fase->id == $item->id,
                                'bg-gray-300' => $fase->id != $item->id,
                            ])>
                            <div @class([
                                'h-4 w-4 md:h-6 md:w-6 rounded-full flex items-center justify-center overflow-hidden p-0',
                                'bg-blue-500' => $fase->id == $item->id,
                                'bg-gray-500' => $fase->id != $item->id,
                                'bg-success-500' => $item->getFinishPercent() == 100,
                            ])>
                            </div>
                        </a>
                        <p class="mb-0 w-24 text-center truncate max-md:hidden">
                            {{ $item->name }}
                        </p>
                    </div>

                    @if (!$loop->last)
                        <p
                            class="flex-1 text-center border-2 border-dashed border-blue-500 max-md:w-20 max-md:rotate-90 max-md:mt-4">
                        </p>
                    @endif
                @endforeach
            </div>
            @isset($fase)
                <div class="px-2  w-full md:mt-4 md:hidden h-fit">
                    <h1 class="text-2xl text-slate-600 flex gap-2 items-center justify-between flex-wrap">
                        <span class="break-all">
                            {{ $fase->name }}
                        </span>
                        <small class="text-sm ">
                            Estado: {{ $fase->status->label }} {{ $fase->getFinishPercent() }}
                        </small>
                    </h1>
                    <div class="md:hidden">
                        <p class="mt-4 italic grow break-all md:order-last">
                            {{ $fase->description }}
                        </p>
                    </div>
                    <a href="{{ route('documents.by-fase', ['faseId' => $fase->id]) }}"
                        class="mt-4 text-xs text-blue-500 float-right flex items-center gap-2 md:hidden">Ver
                        documentos {{-- <iconify-icon icon="formkit:arrowright" /> --}}</a>
                </div>
                <div class="flex gap-11 mt-4 max-md:hidden min-h-[500px]">









                    {{--  --}}
                    <div class="card w-full">
                        <div class="card-body flex flex-col p-6">
                            <header
                                class="flex mb-5 items-center border-b border-slate-100 dark:border-slate-700 pb-5 -mx-6 px-6">
                                <div class="flex-1">
                                    <div class="card-title text-slate-900 dark:text-white">Otras opciones</div>
                                </div>
                            </header>
                            <div class="card-text h-full">
                                <div>


                                    <ul class="nav nav-tabs flex flex-wrap list-none border-b-0 pl-0 mb-4" id="tabs-tab"
                                        role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a href="#tabs-home-withIcon" @class([
                                                'nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent dark:text-slate-300',
                                                'active' => !session('comments'),
                                            ])
                                                id="tabs-home-withIcon-tab" data-bs-toggle="pill"
                                                data-bs-target="#tabs-home-withIcon" role="tab"
                                                aria-controls="tabs-home-withIcon" aria-selected="true">
                                                <iconify-icon icon="ion:document"></iconify-icon>
                                                &nbsp;Documentos</a>
                                        </li>
                                        <li class="nav-item" role="presentation" id="comments-tab"
                                            onclick="handleScrollDown(event)">
                                            <a href="#tabs-profile-withIcon" @class([
                                                'nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent dark:text-slate-300',
                                                'active' => session('comments'),
                                            ])
                                                id="tabs-profile-withIcon-tab" data-bs-toggle="pill"
                                                data-bs-target="#tabs-profile-withIcon" role="tab"
                                                aria-controls="tabs-profile-withIcon" aria-selected="false">
                                                <iconify-icon class="mr-1"
                                                    icon="heroicons-outline:chat-alt-2"></iconify-icon>
                                                &nbsp;Comentarios({{ $qualityControl->comments->whereNull('document_id')->count() }})
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a href="#tabs-messages-withIcon"
                                                class="nav-link w-full flex items-center font-medium text-sm font-Inter leading-tight capitalize border-x-0 border-t-0 border-b border-transparent px-4 pb-2 my-2 hover:border-transparent focus:border-transparent dark:text-slate-300"
                                                id="tabs-messages-withIcon-tab" data-bs-toggle="pill"
                                                data-bs-target="#tabs-messages-withIcon" role="tab"
                                                aria-controls="tabs-messages-withIcon" aria-selected="false">
                                                <iconify-icon icon="material-symbols:history"></iconify-icon>
                                                &nbsp;Historial</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content w-full" id="tabs-tabContent">
                                        {{-- <divddddddddd class="tab-content w-full" id="tabs-tabContent"> --}}
                                            <div @class([
                                                'tab-pane w-full',
                                                ' fade show active ' => !session('comments'),
                                            ]) id="tabs-home-withIcon" role="tabpanel"
                                                aria-labelledby="tabs-home-withIcon-tab">
                                                @foreach ($fase->documents as $item)
                                                    {{-- Formulario de subida para cada documento --}}
                                                    <form enctype="multipart/form-data" method="POST"
                                                        action="{{ route('documents.save-files', $fase) }}" class="mb-4">
                                                        @csrf
                                                        <x-file-picker :document="$item" :fileId="'input-file-' . $loop->iteration"
                                                            :fileName="'files[' . $item->id . ']'" />

                                                        @if ($item->isOpen() || $item->isRejected())
                                                            @hasrole('client')
                                                                <button type="submit" id="upload-btn-{{ $item->id }}"
                                                                    class="btn btn-primary btn-sm mt-2">
                                                                    Subir
                                                                </button>
                                                            @endhasrole
                                                        @endif
                                                    </form>

                                                    {{-- 2) Botón que despliega/oculta comentarios --}}
                                                    <button type="button"
                                                        class="ml-2 mt-2 flex items-center text-sm bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-full px-3 py-1 transition-colors shadow-sm"
                                                        onclick="toggleComments({{ $item->id }})"
                                                        title="Ver comentarios">
                                                        <iconify-icon icon="heroicons-outline:chat-alt-2"
                                                            class="mr-1"></iconify-icon>
                                                        <span
                                                            class="font-semibold">{{ $item->comments()->whereNull('comment_id')->count() }}</span>
                                                    </button>

                                                    {{-- Panel oculto de comentarios --}}
                                                    <div id="comments-{{ $item->id }}"
                                                        class="hidden w-full mt-3 bg-white dark:bg-slate-800 rounded-xl border border-blue-100 dark:border-slate-700 p-4 shadow-lg transition-all duration-300">
                                                        {{-- Cabecera --}}
                                                        <h4
                                                            class="flex items-center gap-2 font-semibold text-blue-700 dark:text-blue-200 mb-3">
                                                            <iconify-icon icon="heroicons-outline:chat-alt-2"
                                                                class="text-xl"></iconify-icon>
                                                            Comentarios
                                                            ({{ $item->comments()->whereNull('comment_id')->count() }})
                                                        </h4>

                                                        {{-- Lista de comentarios --}}
                                                        <div class="space-y-4 max-h-60 overflow-y-auto pr-2">
                                                            @forelse($item->comments()->whereNull('comment_id')->latest()->get() as $comment)
                                                                <div class="flex gap-3 items-start">
                                                                    <img src="{{ $comment->user->avatar ?: Avatar::create($comment->user->name)->toBase64() }}"
                                                                        class="h-10 w-10 rounded-full border-2 border-blue-200"
                                                                        alt="{{ $comment->user->name }}">
                                                                    <div
                                                                        class="flex-1 bg-blue-50 dark:bg-slate-700 rounded-lg p-4 shadow">
                                                                        <div class="flex justify-between items-center">
                                                                            <span
                                                                                class="font-bold text-blue-800 dark:text-blue-200">
                                                                                {{ $comment->user->name }}
                                                                            </span>
                                                                            <span
                                                                                class="text-xs text-gray-500 dark:text-gray-400">
                                                                                {{ $comment->created_at }}
                                                                            </span>
                                                                        </div>
                                                                        <p class="mt-2 text-gray-700 dark:text-gray-200">
                                                                            {{ $comment->comment }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <div
                                                                    class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
                                                                    No hay comentarios en este documento.
                                                                </div>
                                                            @endforelse
                                                        </div>

                                                        {{-- Formulario de nuevo comentario --}}
                                                        {{-- @can('createComment', $qualityControl) --}}
                                                        <form action="{{ route('comments.store') }}" method="POST"
                                                            class="mt-4 flex gap-2">
                                                            @csrf
                                                            <input type="hidden" name="quality_control_id"
                                                                value="{{ $qualityControl->id }}">
                                                            <input type="hidden" name="fase_id"
                                                                value="{{ $fase->id }}">
                                                            <input type="hidden" name="document_id"
                                                                value="{{ $item->id }}">
                                                            <input type="text" name="comment" required
                                                                class="flex-1 rounded-full border border-blue-200 px-4 py-2 shadow focus:outline-none focus:ring-2 focus:ring-blue-400"
                                                                placeholder="Escribe un comentario...">
                                                            <button type="submit"
                                                                class="bg-blue-600 hover:bg-blue-700 text-white rounded-full px-4 py-2 flex items-center gap-1">
                                                                <iconify-icon icon="heroicons-outline:paper-airplane"
                                                                    class="text-lg"></iconify-icon>
                                                                Enviar
                                                            </button>
                                                        </form>
                                                        {{-- @endcan --}}
                                                    </div>
                                                @endforeach
                                                {{-- 
                                                @if ($fase->isOpen() || $fase->isRejected())
                                                    @hasrole('client')
                                                        <button type="submit"
                                                            class="btn btn-primary btn-sm mt-3">Subir</button>
                                                    @endhasrole
                                                @endif --}}
                                        
                                        </div>
                                        
                                        <div @class([
                                            'tab-pane fade',
                                            'show active' => session('comments'),
                                            'h-[500px]' => count($qualityControl->comments),
                                            ]) id="tabs-profile-withIcon" role="tabpanel"
                                            aria-labelledby="tabs-profile-withIcon-tab">
                                            <div @class(['flex flex-col h-full comment-panel reltive'])>
                                                <div class="grow overflow-scroll  h-full" id="comment-area">
                                                    @php
                                                        $generalComments = $qualityControl
                                                            ->comments()
                                                            ->whereNull('document_id')
                                                            ->latest()
                                                            ->get();
                                                    @endphp

                                                    @forelse ($generalComments as $item)
                                                        <x-comment :comment="$item" />
                                                    @empty
                                                        <div class="h-full flex items-center justify-center">
                                                            <p class="text-xl m-auto text-center">
                                                                <img src="{{ asset('images/empty-messages.png') }}"
                                                                    class="h-40 w-40 m-auto" alt="">
                                                                <span class="w-[80%] m-auto">
                                                                    No se han escrito comentarios <br> para este control de
                                                                    calidad
                                                                </span>
                                                            </p>
                                                        </div>
                                                    @endforelse
                                                </div>
                                                <form class="" id="comment-form"
                                                    action="{{ route('comments.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="fase_id" value="{{ $fase->id }}">
                                                    <div class="hidden" id="comment-box-header">
                                                        <div class="card-header flex gap-2 bg-opacity-50">
                                                            <p class="w-full truncate" id="comment-preview"></p>
                                                            <button class="mb-auto" type="button"
                                                                onclick="handleCloseReplyPreview()">
                                                                <iconify-icon class="nav-icon relative top-[2px] leading-3"
                                                                    icon="material-symbols:close"></iconify-icon>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-5">
                                                        <div class="flex items-center gap-2">
                                                            <div class="flex gap-2">
                                                                <div class="w-fit h-fit">
                                                                    <div
                                                                        class="lg:h-10 lg:w-10 h-7 w-7 rounded-full flex-1 bg-gray-500">
                                                                        <img class="block w-full h-full object-cover rounded-full"
                                                                            src="{{ auth()->user()->avatar ?:Avatar::create(auth()->user()->name)->setDimension(400)->setFontSize(240)->toBase64() }}"
                                                                            alt="user" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="grow flex gap-3 border-2 dark:border-[.5px] dark:border-slate-400  rounded-md @error('comment')
                                                            border-danger-500
                                                            @enderror">
                                                                <div class=" grow flex flex-col">
                                                                    <input name="comment" placeholder="Agregar comentario"
                                                                        required
                                                                        class="text-slate-500 focus:outline-none pl-2 pt-2 dark:bg-transparent dark:text-white h-fit w-full" />

                                                                </div>
                                                                <button class="h-fit w-fit p-1">
                                                                    <iconify-icon
                                                                        class="nav-icon text-3xl relative top-[2px] leading-3"
                                                                        icon="material-symbols:send"></iconify-icon>
                                                                </button>
                                                            </div>

                                                        </div>
                                                        <input type="hidden" name="comment_id" id="comment_id">
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <div class="tab-pane fade" id="tabs-messages-withIcon" role="tabpanel"
                                            aria-labelledby="tabs-messages-withIcon-tab">
                                            <div
                                                class="max-h-[400px] overflow-y-auto text-slate-800 block w-full px-4 py-2 text-sm relative">
                                                @foreach ($qualityControl->histories as $item)
                                                    <div
                                                        class="flex ltr:text-left rtl:text-right mb-2  border-b-[.5px] border-opacity-75 pb-2">
                                                        <div class="flex-none ltr:mr-3 rtl:ml-3">
                                                            <div class="h-8 w-8 bg-white rounded-full">
                                                                <img src="{{ $item->user->avatar ?:Avatar::create(auth()->user()->name)->setDimension(400)->setFontSize(240)->toBase64() }}"
                                                                    alt="user"
                                                                    class="border-white block w-full h-full object-cover rounded-full border">
                                                            </div>
                                                        </div>
                                                        <div class="flex-1">
                                                            <a href="#"
                                                                class="text-slate-600 dark:text-slate-300 text-sm font-medium mb-1 before:w-full before:h-full before:absolute
                                                    before:top-0 before:left-0">
                                                                {{ $item->user->name }}</a>
                                                            <div
                                                                class="text-slate-500 dark:text-slate-200 text-xs leading-4">
                                                                {{ $item->description }}</div>
                                                            <div class="text-slate-400 dark:text-slate-400 text-xs mt-1">
                                                                {{ $item->created_at }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>














                    {{-- Componente derecho  --}}
                    <div class="card min-w-[400px] h-fit max-w-[400px] order-second">
                        <div class="card-header">
                            <h4 class="card-title">{{ $fase->name }}</h4>
                        </div>
                        <div class="card-body p-4 pt-1">
                            <div>
                                <div class="flex justify-between text-sm font-normal dark:text-slate-300 mb-3 mt-4">
                                    <span>Descripci&oacute;n</span>
                                </div>
                                <p class="mt-2 italic text-sm mb-1">
                                    {{ $fase->description }}
                                </p>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm font-normal dark:text-slate-300 mb-3 mt-4">
                                    <span>{{ $fase->getDerivedStatusLabel() }}</span>

                                    {{-- <span class="font-normal">{{ $fase->getFinishPercent() }}%</span> --}}
                                </div>
                                <div class="w-full bg-slate-200 h-2 rounded-xl overflow-hidden">
                                    <div class="progress-bar bg-info-500 h-full rounded-xl" style="width:0%;"></div>
                                </div>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-900 rounded p-4 grid grid-cols-2 gap-3 mt-3">
                                <div class="space-y-1">
                                    <h4 class="text-slate-600 dark:text-slate-200 text-xs font-normal">
                                        Documentos a&ntilde;adidos
                                    </h4>
                                    <div class="text-sm font-medium text-slate-900 dark:text-white">
                                        {{ $fase->added_documents }} de {{ count($fase->documents) }} documentos
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 col-span-2">
                                    <a href="?fase={{ $nextFase ? $nextFase->id : $fase->id }}"
                                        class="action-button btn-sm ml-auto text-sm hover:text-blue-500 text-slate-500  ">
                                        Siguiente
                                        <iconify-icon
                                            class="leading-none bg-transparent relative top-[2px] dark:text-white"
                                            icon="ooui:previous-rtl"></iconify-icon>
                                    </a>
                                </div>


                            </div>

                            @role('consultant')
                                <div class="mt-4 border-t pt-3">
                                    <a href="{{ route('quality-controls.export-history', $qualityControl) }}"
                                        id="export-button"
                                        class="flex items-center justify-center w-full px-3 py-2 text-sm rounded-md bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-900/30 dark:text-green-300 dark:hover:bg-green-900/50 transition-colors">
                                        <iconify-icon icon="mdi:file-excel" class="mr-2 text-lg export-icon"></iconify-icon>
                                        <span class="export-text">Exportar Historial</span>
                                    </a>
                                </div>
                            @endrole
                        </div>
                    </div>






                    </ul>
                </div>
            @endisset
        </div>
        
        {{-- Alert end --}}
    </div>

    
    @push('scripts')
        <script>
            function showFormComment() {
                const form = document.getElementById('comment-form');
                form && form.scrollIntoView({
                    behavior: 'smooth'
                })
            }

            function handleScrollDown(event) {
                const div = document.getElementById('comment-area');
                div && setTimeout(() => {
                    div.scrollTop = div.scrollHeight;
                    showFormComment();
                }, 200);
            }


            function toggleComments(docId) {
                document.getElementById('comments-' + docId)?.classList.toggle('hidden');
            }






            window.onload = () => {
                "@if (session('comments'))"
                const div = document.getElementById('comment-area');
                if (div) {
                    div.scrollTop = div.scrollHeight;
                    showFormComment();
                }
                "@endif"
            }

            function handleCloseReplyPreview() {
                const header = document.getElementById('comment-box-header');
                header && !header.classList.contains('hidden') && header.classList.add('hidden')
                const commentId = document.getElementById('comment_id');
                commentId.value = '';
            }

            function handleChange() {
                alert('Changed')
            }

            document.querySelectorAll('#export-button').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.href;

                    Swal.fire({
                        title: '¿Exportar historial?',
                        text: 'Se generará un archivo Excel con todo el historial de esta auditoría',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Exportar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const icon = button.querySelector('.export-icon');
                            const originalIcon = icon.getAttribute('icon');

                            icon.setAttribute('icon', 'eos-icons:loading');
                            icon.classList.add('animate-spin');
                            button.classList.add('opacity-75', 'cursor-not-allowed');

                            window.location.href = url;

                            // Fallback en caso de que la descarga no se inicie
                            setTimeout(() => {
                                icon.setAttribute('icon', originalIcon);
                                icon.classList.remove('animate-spin');
                                button.classList.remove('opacity-75', 'cursor-not-allowed');
                            }, 5000);
                        }
                    });
                });
            });
        </script>
        <script type="module">
            // Progress bar
            $(".progress-bar").animate({
                    width: "{{ $fase->getFinishPercent() }}%",
                },
                1500
            );
            initTooltip()

            function sweetAlertDelete(event, formId) {
                event.preventDefault();
                let form = document.getElementById(formId);
                Swal.fire({
                    title: 'undefined',
                    icon: 'question',
                    showDenyButton: true,
                    confirmButtonText: 'undefined',
                    denyButtonText: 'undefined',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            }

            @push('scripts')
                <script >
                    function toggleComments(docId) {
                        const el = document.getElementById('comments-' + docId);
                        if (!el) return;
                        el.classList.toggle('hidden');
                    }
        </script>

    @endpush
    </script>
@endpush
</x-app-layout>
