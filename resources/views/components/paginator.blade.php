@props(['paginator'])

@if ($paginator->hasPages())
    <x-ui.pagination>
        <x-ui.pagination-content>
            <x-ui.pagination-item>
                @if ($paginator->onFirstPage())
                    <x-ui.pagination-link size="default" aria-disabled="true" class="pointer-events-none gap-1 px-2.5 opacity-50">
                        <x-lucide-chevron-left class="rtl:rotate-180" />
                        <span class="hidden sm:block">{{ __('Previous') }}</span>
                    </x-ui.pagination-link>
                @else
                    <x-ui.pagination-previous :href="$paginator->previousPageUrl()" />
                @endif
            </x-ui.pagination-item>

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                <x-ui.pagination-item>
                    <x-ui.pagination-link :href="$url" :is-active="$page === $paginator->currentPage()">{{ $page }}</x-ui.pagination-link>
                </x-ui.pagination-item>
            @endforeach

            <x-ui.pagination-item>
                @if ($paginator->hasMorePages())
                    <x-ui.pagination-next :href="$paginator->nextPageUrl()" />
                @else
                    <x-ui.pagination-link size="default" aria-disabled="true" class="pointer-events-none gap-1 px-2.5 opacity-50">
                        <span class="hidden sm:block">{{ __('Next') }}</span>
                        <x-lucide-chevron-right class="rtl:rotate-180" />
                    </x-ui.pagination-link>
                @endif
            </x-ui.pagination-item>
        </x-ui.pagination-content>
    </x-ui.pagination>
@endif
