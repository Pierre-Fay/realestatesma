@props(['categories', 'variant' => 'full'])

@php
    $sortOptions = [
        'newest' => __('Newest'),
        'price_asc' => __('Price: low to high'),
        'price_desc' => __('Price: high to low'),
    ];
@endphp

@if ($variant === 'compact')
    <form method="GET" action="{{ route('properties.index') }}" class="bg-card grid gap-3 rounded-xl border p-3 shadow-sm sm:grid-cols-[1fr_10rem_10rem_auto] sm:items-center">
        <x-ui.input name="q" type="search" :value="request('q')" placeholder="{{ __('Search by name or address') }}" aria-label="{{ __('Search') }}" />

        <x-ui.select :native="true" name="type" aria-label="{{ __('Type') }}">
            <option value="">{{ __('Any type') }}</option>
            @foreach ($categories['type'] as $category)
                <option value="{{ $category->id }}" @selected((string) request('type') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </x-ui.select>

        <x-ui.select :native="true" name="area" aria-label="{{ __('Area') }}">
            <option value="">{{ __('Any area') }}</option>
            @foreach ($categories['area'] as $category)
                <option value="{{ $category->id }}" @selected((string) request('area') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </x-ui.select>

        <x-ui.button type="submit">{{ __('Search') }}</x-ui.button>
    </form>
@else
    <form method="GET" action="{{ route('properties.index') }}" class="bg-card grid gap-4 rounded-xl border p-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.field class="sm:col-span-2 lg:col-span-4">
            <x-ui.field-label for="q">{{ __('Search') }}</x-ui.field-label>
            <x-ui.input id="q" name="q" type="search" :value="request('q')" placeholder="{{ __('Name or address') }}" />
        </x-ui.field>

        <x-ui.field>
            <x-ui.field-label for="type">{{ __('Type') }}</x-ui.field-label>
            <x-ui.select :native="true" id="type" name="type">
                <option value="">{{ __('Any type') }}</option>
                @foreach ($categories['type'] as $category)
                    <option value="{{ $category->id }}" @selected((string) request('type') === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </x-ui.select>
        </x-ui.field>

        <x-ui.field>
            <x-ui.field-label for="area">{{ __('Area') }}</x-ui.field-label>
            <x-ui.select :native="true" id="area" name="area">
                <option value="">{{ __('Any area') }}</option>
                @foreach ($categories['area'] as $category)
                    <option value="{{ $category->id }}" @selected((string) request('area') === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </x-ui.select>
        </x-ui.field>

        <x-ui.field>
            <x-ui.field-label for="status">{{ __('Status') }}</x-ui.field-label>
            <x-ui.select :native="true" id="status" name="status">
                <option value="">{{ __('Any status') }}</option>
                @foreach ($categories['status'] as $category)
                    <option value="{{ $category->id }}" @selected((string) request('status') === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </x-ui.select>
        </x-ui.field>

        <x-ui.field>
            <x-ui.field-label for="bedrooms">{{ __('Bedrooms') }}</x-ui.field-label>
            <x-ui.select :native="true" id="bedrooms" name="bedrooms">
                <option value="">{{ __('Any') }}</option>
                @foreach ([1, 2, 3, 4, 5] as $minimum)
                    <option value="{{ $minimum }}" @selected((string) request('bedrooms') === (string) $minimum)>{{ $minimum }}+</option>
                @endforeach
            </x-ui.select>
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
@endif
