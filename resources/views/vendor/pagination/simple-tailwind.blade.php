@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between px-4 gap-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center text-lg font-medium text-gray-500 bg-white cursor-default leading-5 rounded-md">
                <iconify-icon class=" nav-icon" icon="fa-solid:angle-left"></iconify-icon>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center text-lg font-medium text-gray-700 bg-white leading-5 rounded-md hover:text-gray-500 focus:outline-none  ring-gray-300  active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                <iconify-icon class=" nav-icon" icon="fa-solid:angle-left"></iconify-icon>
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center text-lg font-medium text-gray-700 bg-white leading-5 rounded-md hover:text-gray-500 focus:outline-none  ring-gray-300  active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                <iconify-icon class=" nav-icon" icon="fa-solid:angle-right"></iconify-icon>
            </a>
        @else
            <span class="relative inline-flex items-center text-lg font-medium text-gray-500 bg-white  cursor-default leading-5 rounded-md">
                <iconify-icon class=" nav-icon" icon="fa-solid:angle-right"></iconify-icon>
            </span>
            
        @endif
    </nav>
@endif
