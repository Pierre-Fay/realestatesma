@php
    $images = $property->images;
    $cover = $images->firstWhere('type', \App\Enums\PropertyImageType::FEATURED_IMAGE) ?? $images->first();

    $type = $property->categories->firstWhere('group_type', \App\Enums\CategoryGroupType::PROPERTY_TYPE);
    $area = $property->categories->firstWhere('group_type', \App\Enums\CategoryGroupType::PROPERTY_AREA);
    $status = $property->categories->firstWhere('group_type', \App\Enums\CategoryGroupType::PROPERTY_STATUS);
    $features = $property->categories->where('group_type', \App\Enums\CategoryGroupType::PROPERTY_FEATURE);
    $labels = $property->categories->where('group_type', \App\Enums\CategoryGroupType::PROPERTY_LABEL);

    $mapQuery = ($property->latitude && $property->longitude)
        ? $property->latitude.','.$property->longitude
        : collect([$property->address, $property->city, $property->state, $property->country])->filter()->implode(', ');
    $mapUrl = 'https://www.google.com/maps/search/?api=1&query='.urlencode($mapQuery);

    $facts = array_values(array_filter([
        ['label' => __('Bedrooms'), 'value' => $property->bedrooms],
        ['label' => __('Bathrooms'), 'value' => $property->bathrooms],
        $property->half_bathrooms ? ['label' => __('Half baths'), 'value' => $property->half_bathrooms] : null,
        $property->parking_spaces ? ['label' => __('Parking'), 'value' => $property->parking_spaces] : null,
        ['label' => __('Lot (m²)'), 'value' => number_format((float) $property->lot_meters)],
        ['label' => __('Construction (m²)'), 'value' => number_format((float) $property->construction_meters)],
    ]));
@endphp

<x-public-layout :title="$property->name">
    <div class="flex flex-col gap-6">
        <a href="{{ route('properties.index') }}" class="text-muted-foreground hover:text-foreground inline-flex items-center gap-1 text-sm">
            <x-lucide-arrow-left class="size-4 rtl:rotate-180" />
            {{ __('All properties') }}
        </a>

        <div class="flex flex-col gap-3" x-data="{ active: @js($cover?->url) }">
            <div class="bg-muted aspect-[16/9] overflow-hidden rounded-xl border">
                @if ($cover)
                    <img :src="active" src="{{ $cover->url }}" alt="{{ $property->name }}" class="h-full w-full object-cover" />
                @else
                    <div class="flex h-full items-center justify-center">
                        <x-lucide-image class="text-muted-foreground size-10" aria-hidden="true" />
                    </div>
                @endif
            </div>

            @if ($images->count() > 1)
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-5">
                    @foreach ($images as $image)
                        <button
                            type="button"
                            @click="active = @js($image->url)"
                            :class="active === @js($image->url) ? 'ring-primary ring-2' : 'opacity-70 hover:opacity-100'"
                            class="bg-muted aspect-[4/3] overflow-hidden rounded-lg border transition"
                            aria-label="{{ __('View photo') }}"
                        >
                            <img src="{{ $image->url }}" alt="" class="h-full w-full object-cover" />
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="grid gap-8 lg:grid-cols-[1fr_20rem]">
            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <h1 class="text-2xl font-semibold tracking-tight">{{ $property->name }}</h1>
                    <p class="text-muted-foreground">
                        {{ $property->address }}@if ($property->address), @endif{{ $property->city }}, {{ $property->state }}
                    </p>
                    @if ($type || $area || $status || $labels->isNotEmpty())
                        <div class="flex flex-wrap gap-1">
                            @if ($type)<x-ui.badge variant="secondary">{{ $type->name }}</x-ui.badge>@endif
                            @if ($area)<x-ui.badge variant="outline">{{ $area->name }}</x-ui.badge>@endif
                            @if ($status)<x-ui.badge tone="info">{{ $status->name }}</x-ui.badge>@endif
                            @foreach ($labels as $label)<x-ui.badge tone="success">{{ $label->name }}</x-ui.badge>@endforeach
                        </div>
                    @endif
                </div>

                <x-ui.card>
                    <dl class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                        @foreach ($facts as $fact)
                            <div>
                                <dt class="text-muted-foreground text-xs">{{ $fact['label'] }}</dt>
                                <dd class="text-lg font-semibold tabular-nums">{{ $fact['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </x-ui.card>

                @if ($property->description)
                    <div class="flex flex-col gap-2">
                        <h2 class="text-lg font-semibold">{{ __('Description') }}</h2>
                        <p class="text-muted-foreground whitespace-pre-line">{{ $property->description }}</p>
                    </div>
                @endif

                @if ($features->isNotEmpty())
                    <div class="flex flex-col gap-2">
                        <h2 class="text-lg font-semibold">{{ __('Features') }}</h2>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($features as $feature)<x-ui.badge variant="outline">{{ $feature->name }}</x-ui.badge>@endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside class="flex flex-col gap-4 lg:sticky lg:top-6 lg:self-start">
                <x-ui.card>
                    <p class="text-2xl font-semibold tabular-nums">
                        ${{ number_format((float) $property->price_usd) }}
                        <span class="text-muted-foreground text-sm font-normal">USD</span>
                    </p>
                    @if ($property->show_both_prices)
                        <p class="text-muted-foreground text-sm tabular-nums">MX${{ number_format((float) $property->price_mxn) }}</p>
                    @endif
                </x-ui.card>

                <x-ui.card>
                    <div class="flex flex-col gap-2">
                        <h2 class="text-sm font-semibold">{{ __('Location') }}</h2>
                        <p class="text-muted-foreground text-sm">
                            {{ $property->address }}<br />
                            {{ $property->city }}, {{ $property->state }}, {{ $property->country }}
                        </p>
                        <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="text-primary inline-flex items-center gap-1 text-sm font-medium">
                            <x-lucide-map-pin class="size-4" />
                            {{ __('View on map') }}
                        </a>
                    </div>
                </x-ui.card>

                @foreach ($property->agents as $agent)
                    @php
                        $initials = collect(preg_split('/\s+/', trim((string) $agent->name)))
                            ->filter()
                            ->take(2)
                            ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
                            ->implode('');
                    @endphp
                    <x-ui.card>
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <x-ui.avatar class="size-10 rounded-lg">
                                    @if ($agent->photo)<x-ui.avatar-image :src="$agent->photo" :alt="$agent->name" />@endif
                                    <x-ui.avatar-fallback class="rounded-lg">{{ $initials }}</x-ui.avatar-fallback>
                                </x-ui.avatar>
                                <div>
                                    <p class="font-medium">{{ $agent->name }}</p>
                                    <p class="text-muted-foreground text-xs">{{ __('Listing agent') }}</p>
                                </div>
                            </div>

                            @if ($agent->bio)
                                <p class="text-muted-foreground text-sm">{{ $agent->bio }}</p>
                            @endif

                            <div class="flex flex-col gap-1 text-sm">
                                @if ($agent->phone)
                                    <a href="tel:{{ $agent->phone }}" class="text-primary">{{ $agent->phone }}</a>
                                @endif
                                @if ($agent->email)
                                    <a href="mailto:{{ $agent->email }}" class="text-primary">{{ $agent->email }}</a>
                                @endif
                            </div>
                        </div>
                    </x-ui.card>
                @endforeach
            </aside>
        </div>
    </div>
</x-public-layout>
