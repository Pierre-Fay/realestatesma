@php
    $flags = [
        ['code' => 'us', 'name' => __('United States')],
        ['code' => 'mx', 'name' => __('Mexico')],
        ['code' => 'fr', 'name' => __('France')],
        ['code' => 'de', 'name' => __('Germany')],
        ['code' => 'it', 'name' => __('Italy')],
    ];
@endphp

<x-public-layout :title="config('app.name')">
    <x-slot name="hero">
        <section
            class="bg-muted relative h-[60vh] min-h-[30rem] w-full overflow-hidden"
            x-data="{
                active: 0,
                total: {{ $slides->count() }},
                timer: null,
                next() { this.active = (this.active + 1) % this.total },
                prev() { this.active = (this.active - 1 + this.total) % this.total },
                go(i) { this.active = i },
                play() { if (this.total > 1) { this.timer = setInterval(() => this.next(), 5000) } },
                stop() { clearInterval(this.timer) },
                init() { this.play() }
            }"
            x-on:mouseenter="stop()"
            x-on:mouseleave="play()"
        >
            @forelse ($slides as $index => $property)
                @php
                    $cover = $property->images->firstWhere('type', \App\Enums\PropertyImageType::FEATURED_IMAGE) ?? $property->images->first();
                @endphp
                <a
                    href="{{ route('properties.show', $property) }}"
                    x-show="active === {{ $index }}"
                    x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    @if ($index !== 0) style="display: none;" @endif
                    class="absolute inset-0"
                >
                    @if ($cover)
                        <img src="{{ $cover->url }}" alt="{{ $property->name }}" class="h-full w-full object-cover" />
                    @else
                        <div class="from-primary/80 to-primary h-full w-full bg-gradient-to-br"></div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>

                    <div class="absolute inset-x-0 top-8 mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
                        <p class="text-sm font-medium text-white/80">{{ __('Featured listing') }}</p>
                        <p class="text-2xl font-semibold text-white sm:text-3xl">{{ $property->name }}</p>
                        <p class="font-medium text-white">${{ number_format((float) $property->price_usd) }} USD</p>
                    </div>
                </a>
            @empty
                <div class="from-primary/80 to-primary h-full w-full bg-gradient-to-br"></div>
            @endforelse

            @if ($slides->count() > 1)
                <x-ui.button type="button" size="icon" variant="secondary" @click="prev()" class="absolute start-4 top-1/2 -translate-y-1/2 rounded-full" aria-label="{{ __('Previous listing') }}">
                    <x-lucide-chevron-left class="rtl:rotate-180" />
                </x-ui.button>
                <x-ui.button type="button" size="icon" variant="secondary" @click="next()" class="absolute end-4 top-1/2 -translate-y-1/2 rounded-full" aria-label="{{ __('Next listing') }}">
                    <x-lucide-chevron-right class="rtl:rotate-180" />
                </x-ui.button>

                <div class="absolute inset-x-0 bottom-28 flex justify-center gap-2">
                    @foreach ($slides as $index => $property)
                        <button
                            type="button"
                            @click="go({{ $index }})"
                            :class="active === {{ $index }} ? 'w-6 bg-white' : 'w-2 bg-white/50'"
                            class="h-2 rounded-full transition-all"
                            aria-label="{{ __('Go to listing :n', ['n' => $index + 1]) }}"
                        ></button>
                    @endforeach
                </div>
            @endif

            <div class="absolute inset-x-0 bottom-6 mx-auto hidden w-full max-w-3xl px-4 sm:block">
                <x-property-search variant="compact" :categories="$categories" />
            </div>
        </section>

        <div class="mx-auto w-full max-w-6xl px-4 pt-6 sm:hidden">
            <x-property-search variant="compact" :categories="$categories" />
        </div>
    </x-slot>

    <div class="flex flex-col gap-16">
        <section class="flex flex-col items-center gap-4 text-center">
            <h2 class="text-2xl font-semibold tracking-tight">{{ __('Luxury real estate in San Miguel de Allende') }}</h2>
            <p class="text-muted-foreground max-w-2xl">
                {{ __('Real Estate SMA is a boutique agency specializing in luxury villas, restored colonial homes and development land in one of Mexico\'s most sought-after destinations.') }}
            </p>
            <x-ui.button :href="route('about')" variant="outline">{{ __('About us') }}</x-ui.button>
        </section>

        <section class="bg-card flex flex-col items-center gap-6 rounded-xl border p-8 text-center">
            <h2 class="text-xl font-semibold">{{ __('We are multilingual & multicultural') }}</h2>
            <p class="text-muted-foreground max-w-2xl">
                {{ __('Our team speaks English, Spanish, French, German and Italian, and welcomes buyers and investors from around the world.') }}
            </p>
            <div class="flex flex-wrap items-center justify-center gap-6">
                @foreach ($flags as $flag)
                    <div class="flex flex-col items-center gap-2">
                        <img src="{{ asset('images/flags/'.$flag['code'].'.svg') }}" alt="{{ $flag['name'] }}" class="h-7 w-auto rounded-sm shadow-sm" />
                        <span class="text-muted-foreground text-sm">{{ $flag['name'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="flex flex-col items-center gap-4 text-center">
            <h2 class="text-2xl font-semibold tracking-tight">{{ __('Ready to find your place?') }}</h2>
            <div class="flex flex-wrap justify-center gap-2">
                <x-ui.button :href="route('properties.index')">{{ __('Browse properties') }}</x-ui.button>
                <x-ui.button :href="route('contact')" variant="outline">{{ __('Contact us') }}</x-ui.button>
            </div>
        </section>
    </div>
</x-public-layout>
