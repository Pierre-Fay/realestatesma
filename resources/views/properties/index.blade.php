<x-public-layout :title="__('Properties')">
    <div class="flex flex-col gap-6">
        <header>
            <h1 class="text-2xl font-semibold tracking-tight">{{ __('Properties') }}</h1>
            <p class="text-muted-foreground text-sm">{{ __('Find your place in San Miguel de Allende.') }}</p>
        </header>

        @php
            $bedroomOptions = ['' => __('Any')] + [1 => '1+', 2 => '2+', 3 => '3+', 4 => '4+', 5 => '5+'];
            $sortOptions = [
                'newest' => __('Newest'),
                'price_asc' => __('Price: low to high'),
                'price_desc' => __('Price: high to low'),
            ];
        @endphp

        <form method="GET" action="{{ route('properties.index') }}" class="bg-card grid gap-4 rounded-xl border p-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.field class="sm:col-span-2 lg:col-span-4">
                <x-ui.field-label for="q">{{ __('Search') }}</x-ui.field-label>
                <x-ui.input id="q" name="q" type="search" :value="request('q')" placeholder="{{ __('Name or address') }}" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="type">{{ __('Type') }}</x-ui.field-label>
                <x-ui.select :native="true" id="type" name="type" :value="request('type')" :options="['' => __('Any type')] + $categories['type']->pluck('name', 'id')->all()" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="area">{{ __('Area') }}</x-ui.field-label>
                <x-ui.select :native="true" id="area" name="area" :value="request('area')" :options="['' => __('Any area')] + $categories['area']->pluck('name', 'id')->all()" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="status">{{ __('Status') }}</x-ui.field-label>
                <x-ui.select :native="true" id="status" name="status" :value="request('status')" :options="['' => __('Any status')] + $categories['status']->pluck('name', 'id')->all()" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="bedrooms">{{ __('Bedrooms') }}</x-ui.field-label>
                <x-ui.select :native="true" id="bedrooms" name="bedrooms" :value="request('bedrooms')" :options="$bedroomOptions" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="price_min">{{ __('Min price (USD)') }}</x-ui.field-label>
                <x-ui.input id="price_min" name="price_min" type="number" min="0" step="1000" :value="request('price_min')" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="price_max">{{ __('Max price (USD)') }}</x-ui.field-label>
                <x-ui.input id="price_max" name="price_max" type="number" min="0" step="1000" :value="request('price_max')" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="sort">{{ __('Sort by') }}</x-ui.field-label>
                <x-ui.select :native="true" id="sort" name="sort" :value="request('sort', 'newest')" :options="$sortOptions" />
            </x-ui.field>

            <div class="flex items-end gap-2">
                <x-ui.button type="submit">{{ __('Apply') }}</x-ui.button>
                <x-ui.button :href="route('properties.index')" variant="ghost">{{ __('Reset') }}</x-ui.button>
            </div>
        </form>

        <p class="text-muted-foreground text-sm">
            {{ $properties->total() }} {{ $properties->total() === 1 ? __('property') : __('properties') }}
        </p>

        @if ($properties->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($properties as $property)
                    <x-property-card :property="$property" />
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
