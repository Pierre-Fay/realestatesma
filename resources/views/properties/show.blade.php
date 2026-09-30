@php
    $images = $property->images;

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

        <div
            x-data="{
                active: 0,
                total: {{ $images->count() }},
                prev() { this.active = (this.active - 1 + this.total) % this.total; this.reveal() },
                next() { this.active = (this.active + 1) % this.total; this.reveal() },
                go(i) { this.active = i; this.reveal() },
                reveal() { this.$refs.strip?.children[this.active]?.scrollIntoView({ inline: 'center', block: 'nearest', behavior: 'smooth' }) },
                overflow: false,
                measure() { this.overflow = !! this.$refs.strip && this.$refs.strip.scrollWidth > this.$refs.strip.clientWidth + 1 },
                nudge(dir) { this.$refs.strip?.scrollBy({ left: dir * this.$refs.strip.clientWidth * 0.8, behavior: 'smooth' }) },
                init() { this.$nextTick(() => this.measure()) }
            }"
            x-on:resize.window="measure()"
            class="flex flex-col gap-3"
        >
            <div class="bg-muted relative overflow-hidden rounded-xl border">
                @forelse ($images as $index => $image)
                    <img
                        x-show="active === {{ $index }}"
                        @if ($index !== 0) style="display: none;" @endif
                        src="{{ $image->url }}"
                        alt="{{ $property->name }}"
                        class="block h-[clamp(16rem,45vh,28rem)] w-full object-cover"
                    />
                @empty
                    <div class="flex h-[clamp(16rem,45vh,28rem)] items-center justify-center">
                        <x-lucide-image class="text-muted-foreground size-10" aria-hidden="true" />
                    </div>
                @endforelse

                @if ($images->count() > 1)
                    <x-ui.button type="button" size="icon" variant="secondary" @click="prev()" class="absolute start-3 top-1/2 -translate-y-1/2 rounded-full" aria-label="{{ __('Previous photo') }}">
                        <x-lucide-chevron-left class="rtl:rotate-180" />
                    </x-ui.button>
                    <x-ui.button type="button" size="icon" variant="secondary" @click="next()" class="absolute end-3 top-1/2 -translate-y-1/2 rounded-full" aria-label="{{ __('Next photo') }}">
                        <x-lucide-chevron-right class="rtl:rotate-180" />
                    </x-ui.button>
                @endif
            </div>

            @if ($images->count() > 1)
                <div class="flex items-center gap-2">
                    <x-ui.button type="button" size="icon-sm" variant="ghost" x-show="overflow" x-cloak @click="nudge(-1)" class="shrink-0" aria-label="{{ __('Scroll photos left') }}">
                        <x-lucide-chevron-left class="rtl:rotate-180" />
                    </x-ui.button>

                    <div x-ref="strip" class="flex flex-1 gap-2 overflow-x-auto scroll-smooth [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                        @foreach ($images as $index => $image)
                            <button
                                type="button"
                                @click="go({{ $index }})"
                                :class="active === {{ $index }} ? 'border-primary' : 'border-transparent opacity-70 hover:opacity-100'"
                                class="bg-muted aspect-[4/3] h-16 shrink-0 overflow-hidden rounded-lg border-2 transition sm:h-20"
                                aria-label="{{ __('View photo :number', ['number' => $index + 1]) }}"
                            >
                                <img src="{{ $image->url }}" alt="" class="h-full w-full object-cover" />
                            </button>
                        @endforeach
                    </div>

                    <x-ui.button type="button" size="icon-sm" variant="ghost" x-show="overflow" x-cloak @click="nudge(1)" class="shrink-0" aria-label="{{ __('Scroll photos right') }}">
                        <x-lucide-chevron-right class="rtl:rotate-180" />
                    </x-ui.button>
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

                <x-ui.card>
                    <x-ui.card-header>
                        <x-ui.card-title>{{ __('Enquire about this property') }}</x-ui.card-title>
                    </x-ui.card-header>
                    <x-ui.card-content class="flex flex-col gap-4">
                        @if (session('status') === 'inquiry-sent')
                            <x-ui.alert tone="success">
                                <x-lucide-circle-check />
                                <x-ui.alert-title>{{ __('Thanks! Your message has been sent.') }}</x-ui.alert-title>
                            </x-ui.alert>
                        @endif

                        <form method="POST" action="{{ route('properties.inquiries.store', $property) }}" class="flex flex-col gap-4">
                            @csrf

                            <x-ui.field>
                                <x-ui.field-label for="name">{{ __('Name') }}</x-ui.field-label>
                                <x-ui.input id="name" name="name" type="text" :value="old('name')" required />
                                <x-ui.field-error>{{ $errors->first('name') }}</x-ui.field-error>
                            </x-ui.field>

                            <x-ui.field>
                                <x-ui.field-label for="email">{{ __('Email') }}</x-ui.field-label>
                                <x-ui.input id="email" name="email" type="email" :value="old('email')" required />
                                <x-ui.field-error>{{ $errors->first('email') }}</x-ui.field-error>
                            </x-ui.field>

                            <x-ui.field>
                                <x-ui.field-label for="phone">{{ __('Phone') }}</x-ui.field-label>
                                <x-ui.input id="phone" name="phone" type="tel" :value="old('phone')" />
                                <x-ui.field-error>{{ $errors->first('phone') }}</x-ui.field-error>
                            </x-ui.field>

                            <x-ui.field>
                                <x-ui.field-label for="message">{{ __('Message') }}</x-ui.field-label>
                                <x-ui.textarea id="message" name="message" rows="4" required>{{ old('message') }}</x-ui.textarea>
                                <x-ui.field-error>{{ $errors->first('message') }}</x-ui.field-error>
                            </x-ui.field>

                            <x-ui.field>
                                {{-- TODO(CMS-01): link "privacy policy" once the static page is published. --}}
                                <x-ui.label class="items-start">
                                    <x-ui.checkbox :native="true" name="consent" value="1" :checked="(bool) old('consent')" class="mt-0.5" />
                                    {{ __('I agree to be contacted and to the processing of my personal data.') }}
                                </x-ui.label>
                                <x-ui.field-error>{{ $errors->first('consent') }}</x-ui.field-error>
                            </x-ui.field>

                            {{-- Honeypot: hidden from humans, tempting to bots. --}}
                            <div class="hidden" aria-hidden="true">
                                <label>
                                    {{ __('Website') }}
                                    <input type="text" name="website" tabindex="-1" autocomplete="off" />
                                </label>
                            </div>

                            <x-ui.button type="submit" class="w-full">{{ __('Send enquiry') }}</x-ui.button>
                        </form>
                    </x-ui.card-content>
                </x-ui.card>
            </aside>
        </div>
    </div>
</x-public-layout>
