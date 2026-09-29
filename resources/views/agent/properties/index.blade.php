<x-app-layout>
    <x-slot name="header">{{ __('My listings') }}</x-slot>

    <div class="flex flex-col gap-4">
        @if (session('status'))
            <x-ui.alert tone="success">
                <x-lucide-circle-check />
                <x-ui.alert-title>
                    {{ session('status') === 'listing-created' ? __('Listing created.') : __('Listing updated.') }}
                </x-ui.alert-title>
            </x-ui.alert>
        @endif

        <div class="flex items-center justify-between gap-4">
            <p class="text-muted-foreground text-sm">
                {{ __('Your property listings. New listings are reviewed by an admin before going live.') }}
            </p>
            <x-ui.button :href="route('listings.create')" size="sm">
                <x-lucide-plus />
                {{ __('New listing') }}
            </x-ui.button>
        </div>

        <x-ui.table variant="card">
            <x-ui.table-header>
                <x-ui.table-row>
                    <x-ui.table-head>{{ __('Listing') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Type') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Area') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Price (USD)') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Status') }}</x-ui.table-head>
                    <x-ui.table-head class="text-end">{{ __('Actions') }}</x-ui.table-head>
                </x-ui.table-row>
            </x-ui.table-header>
            <x-ui.table-body>
                @forelse ($properties as $property)
                    @php
                        $cover = $property->images->firstWhere('type', \App\Enums\PropertyImageType::FEATURED_IMAGE) ?? $property->images->first();
                        $type = $property->categories->firstWhere('group_type', \App\Enums\CategoryGroupType::PROPERTY_TYPE);
                        $area = $property->categories->firstWhere('group_type', \App\Enums\CategoryGroupType::PROPERTY_AREA);
                    @endphp
                    <x-ui.table-row>
                        <x-ui.table-cell>
                            <div class="flex items-center gap-3">
                                @if ($cover)
                                    <img src="{{ Storage::disk('public')->url($cover->path) }}" alt="" class="size-10 rounded-md object-cover" />
                                @else
                                    <div class="bg-muted flex size-10 items-center justify-center rounded-md">
                                        <x-lucide-image class="text-muted-foreground size-4" />
                                    </div>
                                @endif
                                <span class="font-medium">{{ $property->name }}</span>
                            </div>
                        </x-ui.table-cell>
                        <x-ui.table-cell>{{ $type?->name ?? '—' }}</x-ui.table-cell>
                        <x-ui.table-cell>{{ $area?->name ?? '—' }}</x-ui.table-cell>
                        <x-ui.table-cell class="tabular-nums">{{ number_format((float) $property->price_usd) }}</x-ui.table-cell>
                        <x-ui.table-cell>
                            @if ($property->is_sold)
                                <x-ui.badge tone="neutral">{{ __('Sold') }}</x-ui.badge>
                            @elseif ($property->is_active)
                                <x-ui.badge tone="success">{{ __('Live') }}</x-ui.badge>
                            @else
                                <x-ui.badge tone="warning">{{ __('Pending approval') }}</x-ui.badge>
                            @endif
                        </x-ui.table-cell>
                        <x-ui.table-cell class="text-end">
                            <x-ui.button :href="route('listings.edit', $property)" variant="outline" size="sm">{{ __('Edit') }}</x-ui.button>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @empty
                    <x-ui.table-row>
                        <x-ui.table-cell colspan="6" class="text-muted-foreground py-6 text-center">
                            {{ __('No listings yet.') }}
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @endforelse
            </x-ui.table-body>
        </x-ui.table>

        <x-paginator :paginator="$properties" />
    </div>
</x-app-layout>
