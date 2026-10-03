@props(['href', 'title', 'description', 'icon'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group block rounded-xl outline-none focus-visible:ring-ring/50 focus-visible:ring-[3px]']) }}>
    <x-ui.card class="flex h-full flex-col gap-4 transition-colors group-hover:border-primary/50 group-hover:bg-accent/30">
        <div class="flex items-center justify-between gap-3">
            <span class="bg-primary/10 text-primary flex size-10 items-center justify-center rounded-lg">
                <x-dynamic-component :component="$icon" class="size-5" aria-hidden="true" />
            </span>
            <x-lucide-arrow-up-right class="text-muted-foreground size-4 transition-colors group-hover:text-primary" aria-hidden="true" />
        </div>
        <div class="flex flex-col gap-1">
            <h3 class="font-semibold">{{ $title }}</h3>
            <p class="text-muted-foreground text-sm leading-relaxed">{{ $description }}</p>
        </div>
    </x-ui.card>
</a>
