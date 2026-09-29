@php
    $p = $property ?? null;
    $selectedCategories = collect(old('categories', $p?->categories->pluck('id')->all() ?? []))->map(fn ($id) => (int) $id);

    $groupLabels = [
        'property_type' => __('Type'),
        'property_area' => __('Area'),
        'property_feature' => __('Features'),
    ];
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="flex flex-col gap-6">
    @csrf
    @method($method)

    <x-ui.field>
        <x-ui.field-label for="name">{{ __('Name') }}</x-ui.field-label>
        <x-ui.input id="name" name="name" type="text" :value="old('name', $p?->name)" required autofocus />
        <x-ui.field-error>{{ $errors->first('name') }}</x-ui.field-error>
    </x-ui.field>

    <div class="grid gap-6 sm:grid-cols-3">
        <x-ui.field class="sm:col-span-2">
            <x-ui.field-label for="address">{{ __('Address') }}</x-ui.field-label>
            <x-ui.input id="address" name="address" type="text" :value="old('address', $p?->address)" />
            <x-ui.field-error>{{ $errors->first('address') }}</x-ui.field-error>
        </x-ui.field>

        <x-ui.field>
            <x-ui.field-label for="zip">{{ __('ZIP') }}</x-ui.field-label>
            <x-ui.input id="zip" name="zip" type="text" :value="old('zip', $p?->zip)" required />
            <x-ui.field-error>{{ $errors->first('zip') }}</x-ui.field-error>
        </x-ui.field>
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <x-ui.field>
            <x-ui.field-label for="price_usd">{{ __('Price (USD)') }}</x-ui.field-label>
            <x-ui.input id="price_usd" name="price_usd" type="number" step="0.01" min="0" :value="old('price_usd', $p?->price_usd)" required />
            <x-ui.field-error>{{ $errors->first('price_usd') }}</x-ui.field-error>
        </x-ui.field>

        <x-ui.field>
            <x-ui.field-label for="price_mxn">{{ __('Price (MXN)') }}</x-ui.field-label>
            <x-ui.input id="price_mxn" name="price_mxn" type="number" step="0.01" min="0" :value="old('price_mxn', $p?->price_mxn)" required />
            <x-ui.field-error>{{ $errors->first('price_mxn') }}</x-ui.field-error>
        </x-ui.field>
    </div>

    <x-ui.field>
        <x-ui.label>
            <input type="hidden" name="show_both_prices" value="0" />
            <x-ui.checkbox :native="true" name="show_both_prices" value="1" :checked="(bool) old('show_both_prices', $p?->show_both_prices)" />
            {{ __('Show both prices publicly') }}
        </x-ui.label>
    </x-ui.field>

    <div class="grid gap-6 sm:grid-cols-2">
        <x-ui.field>
            <x-ui.field-label for="lot_meters">{{ __('Lot (m²)') }}</x-ui.field-label>
            <x-ui.input id="lot_meters" name="lot_meters" type="number" step="0.01" min="0" :value="old('lot_meters', $p?->lot_meters)" required />
            <x-ui.field-error>{{ $errors->first('lot_meters') }}</x-ui.field-error>
        </x-ui.field>

        <x-ui.field>
            <x-ui.field-label for="construction_meters">{{ __('Construction (m²)') }}</x-ui.field-label>
            <x-ui.input id="construction_meters" name="construction_meters" type="number" step="0.01" min="0" :value="old('construction_meters', $p?->construction_meters)" required />
            <x-ui.field-error>{{ $errors->first('construction_meters') }}</x-ui.field-error>
        </x-ui.field>
    </div>

    <div class="grid gap-6 sm:grid-cols-4">
        @foreach (['bedrooms' => __('Bedrooms'), 'bathrooms' => __('Bathrooms'), 'half_bathrooms' => __('Half baths'), 'parking_spaces' => __('Parking')] as $field => $label)
            <x-ui.field>
                <x-ui.field-label for="{{ $field }}">{{ $label }}</x-ui.field-label>
                <x-ui.input id="{{ $field }}" name="{{ $field }}" type="number" step="1" min="0" :value="old($field, $p?->{$field} ?? 0)" required />
                <x-ui.field-error>{{ $errors->first($field) }}</x-ui.field-error>
            </x-ui.field>
        @endforeach
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <x-ui.field>
            <x-ui.field-label for="latitude">{{ __('Latitude') }}</x-ui.field-label>
            <x-ui.input id="latitude" name="latitude" type="number" step="any" :value="old('latitude', $p?->latitude)" />
            <x-ui.field-error>{{ $errors->first('latitude') }}</x-ui.field-error>
        </x-ui.field>

        <x-ui.field>
            <x-ui.field-label for="longitude">{{ __('Longitude') }}</x-ui.field-label>
            <x-ui.input id="longitude" name="longitude" type="number" step="any" :value="old('longitude', $p?->longitude)" />
            <x-ui.field-error>{{ $errors->first('longitude') }}</x-ui.field-error>
        </x-ui.field>
    </div>

    <x-ui.field>
        <x-ui.field-label for="description">{{ __('Description') }}</x-ui.field-label>
        <x-ui.textarea id="description" name="description" rows="5">{{ old('description', $p?->description) }}</x-ui.textarea>
        <x-ui.field-error>{{ $errors->first('description') }}</x-ui.field-error>
    </x-ui.field>

    @foreach ($groupLabels as $group => $label)
        @if (($categories[$group] ?? collect())->isNotEmpty())
            <x-ui.field>
                <x-ui.field-label>{{ $label }}</x-ui.field-label>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                    @foreach ($categories[$group] as $category)
                        <x-ui.label>
                            <x-ui.checkbox :native="true" name="categories[]" :value="$category->id" :checked="$selectedCategories->contains($category->id)" />
                            {{ $category->name }}
                        </x-ui.label>
                    @endforeach
                </div>
                <x-ui.field-error>{{ $errors->first('categories.*') }}</x-ui.field-error>
            </x-ui.field>
        @endif
    @endforeach

    @if ($p?->exists && $p->images->isNotEmpty())
        <x-ui.field>
            <x-ui.field-label>{{ __('Current images') }}</x-ui.field-label>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ($p->images as $image)
                    <label class="group relative block overflow-hidden rounded-lg border">
                        <img src="{{ Storage::disk('public')->url($image->path) }}" alt="" class="h-28 w-full object-cover" />
                        @if ($image->isFeatured())
                            <x-ui.badge class="absolute start-2 top-2">{{ __('Featured') }}</x-ui.badge>
                        @endif
                        <span class="flex items-center gap-2 p-2 text-xs">
                            <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="blat-checkbox" />
                            {{ __('Remove') }}
                        </span>
                    </label>
                @endforeach
            </div>
            <x-ui.field-description>{{ __('Tick an image to remove it when saving.') }}</x-ui.field-description>
        </x-ui.field>
    @endif

    <div class="grid gap-6 sm:grid-cols-2">
        <x-ui.field>
            <x-ui.field-label for="featured_image">{{ __('Featured image') }}</x-ui.field-label>
            <x-ui.input id="featured_image" name="featured_image" type="file" accept="image/*" />
            <x-ui.field-description>{{ __('Uploading a new one replaces the current featured image.') }}</x-ui.field-description>
            <x-ui.field-error>{{ $errors->first('featured_image') }}</x-ui.field-error>
        </x-ui.field>

        <x-ui.field>
            <x-ui.field-label for="gallery">{{ __('Gallery images') }}</x-ui.field-label>
            <x-ui.input id="gallery" name="gallery[]" type="file" accept="image/*" multiple />
            <x-ui.field-description>{{ __('Up to 10 images, 5 MB each (jpg, png, webp).') }}</x-ui.field-description>
            <x-ui.field-error>{{ $errors->first('gallery.*') }}</x-ui.field-error>
        </x-ui.field>
    </div>

    <div class="flex items-center gap-2">
        <x-ui.button type="submit">{{ $submit }}</x-ui.button>
        <x-ui.button :href="route('listings.index')" variant="ghost">{{ __('Cancel') }}</x-ui.button>
    </div>
</form>
