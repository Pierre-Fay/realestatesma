<x-public-layout :title="__('Properties')">
    <div class="flex flex-col gap-6">
        <header>
            <h1 class="text-2xl font-semibold tracking-tight">{{ __('Properties') }}</h1>
            <p class="text-muted-foreground text-sm">{{ __('Find your place in San Miguel de Allende.') }}</p>
        </header>

        <x-property-search variant="full" :categories="$categories" />

        <p class="text-muted-foreground text-sm">
            {{ $properties->total() }} {{ $properties->total() === 1 ? __('property') : __('properties') }}
        </p>

        @if ($properties->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($properties as $property)
                    <x-property-card :property="$property" :href="route('properties.show', $property)" />
                @endforeach
            </div>

            <x-paginator :paginator="$properties" />
        @else
            <x-ui.empty>
                <x-ui.empty-media><x-lucide-search-x aria-hidden="true" /></x-ui.empty-media>
                <x-ui.empty-header>
                    <x-ui.empty-title>{{ __('No properties found') }}</x-ui.empty-title>
                    <x-ui.empty-description>{{ __('Try adjusting your search or filters.') }}</x-ui.empty-description>
                </x-ui.empty-header>
            </x-ui.empty>
        @endif
    </div>
</x-public-layout>
