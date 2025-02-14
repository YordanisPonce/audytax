@php
    $id = last(request()->segments());
@endphp

<x-app-layout>
    <div class="pb-20">
        <div class=" mb-6 md:mb-2">
            {{-- Breadcrumb start --}}
            <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />

        </div>
        <div class="flex flex-col gap-4 items-center">
            @forelse ($comments as $item)
                <x-comment :comment="$item" />
            @empty
                <p class="text-xl">Se el primero en escribir un comentario</p>
            @endforelse

            <form class="card w-full max-w-[800px]" id="comment-form" action="{{ route('comments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="fase_id" value="{{ $id }}">
                <div class="hidden" id="comment-box-header">
                    <div class="card-header flex gap-2 bg-opacity-50">
                        <p class="w-full truncate" id="comment-preview"></p>
                        <button class="mb-auto" type="button" onclick="handleCloseReplyPreview()">
                            <iconify-icon class="nav-icon relative top-[2px] leading-3"
                                icon="material-symbols:close"></iconify-icon>
                        </button>
                    </div>
                </div>
                <div class="card-body p-5">
                    <div class="flex items-center gap-2">
                        <div class="flex gap-2">
                            <div class="w-fit h-fit">
                                <div class="lg:h-10 lg:w-10 h-7 w-7 rounded-full flex-1 bg-gray-500">
                                    <img class="block w-full h-full object-cover rounded-full"
                                        src="{{ auth()->user()->avatar ?: Avatar::create(auth()->user()->name)->setDimension(400)->setFontSize(240)->toBase64() }}" alt="user" />
                                </div>
                            </div>
                        </div>
                        <div
                            class="grow flex gap-3 border-2 dark:border-[.5px] dark:border-slate-400  rounded-md @error('comment')
                        border-danger-500
                        @enderror">
                            <div class=" grow flex flex-col">
                                <input name="comment" placeholder="Agregar comentario" required
                                    class="text-slate-500 focus:outline-none pl-2 pt-2 dark:bg-transparent dark:text-white h-fit w-full" />

                            </div>
                            <button class="h-fit w-fit p-1">
                                <iconify-icon class="nav-icon text-3xl relative top-[2px] leading-3"
                                    icon="material-symbols:send"></iconify-icon>
                            </button>
                        </div>

                    </div>
                    <input type="hidden" name="comment_id" id="comment_id">
            </form>

        </div>

        @push('scripts')
            <script>
                function handleCloseReplyPreview() {
                    const header = document.getElementById('comment-box-header');
                    header && !header.classList.contains('hidden') && header.classList.add('hidden')
                    const commentId = document.getElementById('comment_id');
                    commentId.value = '';
                }
            </script>
        @endpush
</x-app-layout>
