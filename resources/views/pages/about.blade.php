<x-public-layout :title="__('About us')">
    <div class="mx-auto flex max-w-3xl flex-col gap-6">
        <header class="flex flex-col gap-2">
            <h1 class="text-3xl font-semibold tracking-tight">{{ __('About us') }}</h1>
            <p class="text-muted-foreground">{{ __('Luxury real estate in San Miguel de Allende, Mexico.') }}</p>
        </header>

        <div class="text-muted-foreground flex flex-col gap-4 leading-relaxed">
            <p>
                {{ __('Real Estate SMA is an independent, boutique agency based in San Miguel de Allende, Guanajuato. We help buyers, sellers and investors navigate one of Mexico\'s most sought-after destinations.') }}
            </p>
            <p>
                {{ __('Our portfolio focuses on luxury villas, restored colonial homes, contemporary residences and development land. Every listing is reviewed by our team before it is published so that what you see is accurate, well presented and ready to visit.') }}
            </p>
            <p>
                {{ __('Our bilingual agents live and work in San Miguel de Allende. We know the neighbourhoods, the architecture and the paperwork, and we can guide you from the first viewing to the closing table — whether you are relocating, investing or searching for a holiday home.') }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <x-ui.button :href="route('properties.index')">{{ __('Browse properties') }}</x-ui.button>
            <x-ui.button :href="route('contact')" variant="outline">{{ __('Contact us') }}</x-ui.button>
        </div>
    </div>
</x-public-layout>
