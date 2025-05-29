@props(['comment'])
<div class="w-full max-w-[800px]">
    <div class="card">
        <div class="card-body p-5">
            <div class="flex">
                <div class="flex gap-2 items-center">
                    <div class="w-fit h-fit">
                        <div class="lg:h-8 lg:w-8 h-7 w-7 rounded-full flex-1 bg-gray-500 ltr:mr-[5px] rtl:ml-[10px]">
                            <img class="block w-full h-full object-cover rounded-full" src="{{ $comment->user->avatar ?:Avatar::create(auth()->user()->name)->setDimension(400)->setFontSize(240)->toBase64() }}"
                                alt="user" />
                        </div>
                    </div>
                    <p class="flex max-sm:flex-col sm:items-center sm:gap-2 truncate">
                        <span class="font-bold text-black-500 dark:text-white">
                            {{ $comment->user->name }}
                        </span>
                        <small class="truncate dark:text-gray-500">
                            {{ $comment->created_at }}
                        </small>
                    </p>
                </div>
                @if (!$comment->comment_id)
                    <button onclick="handleNavigateToForm(event, '{{ $comment->id }}')"
                        class="ml-auto h-fit text-slate-500 dark:text-white hover:text-primary-500 dark:hover:text-primary-500 text-xs transition-colors">
                        <iconify-icon class="nav-icon relative top-[2px] leading-3" icon="subway:reply"></iconify-icon>
                        <span class="max-sm:hidden">Responder</span>
                    </button>
                @endif
            </div>
            <p class="mt-3 text-slate-500 dark:text-white comment">
                {{ $comment->comment }}
            </p>
        </div>
    </div>
    <div class="flex items-stretch justify-between">
        <div @class([
            'w-[2px] bg-slate-400 min-h-full ml-8 md:ml-16',
            'mt-3' => count($comment->comments),
        ])>
        </div>
        <div class="w-[80%] flex flex-col gap-3">
            @foreach ($comment->comments as $item)
                <div class="card mt-3">
                    <div class="card-body p-5">
                        <div class="flex">
                            <div class="flex gap-2 items-center">
                                <div class="w-fit h-fit">
                                    <div
                                        class="lg:h-8 lg:w-8 h-7 w-7 rounded-full flex-1 bg-gray-500 ltr:mr-[5px] rtl:ml-[10px]">
                                        <img class="block w-full h-full object-cover rounded-full"
                                            src="{{ $item->user->avatar ?:Avatar::create($item->user->name)->setDimension(400)->setFontSize(240)->toBase64() }}" alt="user" />
                                    </div>
                                </div>
                                <p class="flex max-sm:flex-col sm:items-center sm:gap-2 truncate">
                                    <span class="font-bold text-black-500 dark:text-white">
                                        {{ $item->user->name }}
                                    </span>
                                    <small class="truncate dark:text-gray-500">
                                        {{ $item->created_at }}
                                    </small>
                                </p>
                            </div>
                        </div>
                        <p class="mt-3 text-slate-500 dark:text-white">
                            {{ $item->comment }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>




@push('scripts')
    <script>
        function handleNavigateToForm(event, id) {
            const {
                target
            } = event || window.event;

            const form = document.getElementById('comment-form');
            const header = document.getElementById('comment-box-header');
            //comment-preview
            const p = document.getElementById('comment-preview');
            form && form.scrollIntoView({
                behavior: 'smooth'
            })

            header && header.classList.contains('hidden') && header.classList.remove('hidden')
            const comment = target.closest('div.card-body')?.querySelector('p.comment')?.textContent;
            if (p) {
                p.textContent = comment;
            }

            const commentId = document.getElementById('comment_id');
            commentId.value = id;

        }
    </script>
@endpush
