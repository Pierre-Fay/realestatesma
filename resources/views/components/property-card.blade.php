@props(['property', 'href' => null])

@php
    $cover = $property->images->firstWhere('type', \App\Enums\PropertyImageType::FEATURED_IMAGE) ?? $property->images->first();
    $type = $property->categories->firstWhere('group_type', \App\Enums\CategoryGroupType::PROPERTY_TYPE);
    $area = $property->categories->firstWhere('group_type', \App\Enums\CategoryGroupType::PROPERTY_AREA);
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif class="group block h-full">
    <x-ui.card variant="sectioned" class="h-full gap-0 overflow-hidden pt-0 transition-shadow group-hover:shadow-md">
        <div class="bg-muted aspect-[4/3] overflow-hidden">
            @if ($cover)
                <img
                    src="{{ $cover->url }}"
                    alt="{{ $property->name }}"
                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                />
            @else
                <div class="flex h-full items-center justify-center">
                    <x-lucide-image class="text-muted-foreground size-8" aria-hidden="true" />
                </div>
            @endif
        </div>

        <x-ui.card-header class="pt-6">
            <x-ui.card-title class="truncate">{{ $property->name }}</x-ui.card-title>
            @if ($type || $area)
                <x-ui.card-description class="flex flex-wrap gap-1">
                    @if ($type)<x-ui.badge variant="secondary">{{ $type->name }}</x-ui.badge>@endif
                    @if ($area)<x-ui.badge variant="outline">{{ $area->name }}</x-ui.badge>@endif
                </x-ui.card-description>
            @endif
        </x-ui.card-header>

        <x-ui.card-content class="mt-auto pt-4">
            <p class="text-lg font-semibold tabular-nums">${{ number_format((float) $property->price_usd) }}</p>
            <p class="text-muted-foreground text-sm">
                {{ $property->bedrooms }} {{ __('bd') }} &middot; {{ $property->bathrooms }} {{ __('ba') }} &middot; {{ number_format((float) $property->construction_meters) }} m²
            </p>
        </x-ui.card-content>
    </x-ui.card>
</{{ $tag }}>
