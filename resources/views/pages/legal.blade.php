<x-public-layout :title="__('Legal notice')">
    <div class="mx-auto flex max-w-3xl flex-col gap-6">
        <header class="flex flex-col gap-2">
            <h1 class="text-3xl font-semibold tracking-tight">{{ __('Legal notice') }}</h1>
            <p class="text-muted-foreground">{{ __('Last updated: :date', ['date' => now()->isoFormat('MMMM YYYY')]) }}</p>
        </header>

        <div class="text-muted-foreground flex flex-col gap-6 leading-relaxed">
            <section class="flex flex-col gap-2">
                <h2 class="text-foreground text-lg font-semibold">{{ __('Publisher') }}</h2>
                <p>
                    {{ __('This website is published by Real Estate SMA, a real estate agency based in San Miguel de Allende, Guanajuato, Mexico.') }}
                </p>
                <p>
                    {{ __('Contact: :email', ['email' => 'contact@realestatesma.test']) }}
                </p>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-foreground text-lg font-semibold">{{ __('Intellectual property') }}</h2>
                <p>
                    {{ __('All content on this website — text, photographs, logos and layout — is protected by intellectual property law. Any reproduction or reuse without prior written permission is prohibited.') }}
                </p>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-foreground text-lg font-semibold">{{ __('Liability') }}</h2>
                <p>
                    {{ __('Property information is provided in good faith and may change without notice. We cannot be held liable for any inaccuracy, omission or for decisions made on the basis of the information published here.') }}
                </p>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-foreground text-lg font-semibold">{{ __('Governing law') }}</h2>
                <p>
                    {{ __('This website and any dispute arising from its use are governed by the laws of the United Mexican States.') }}
                </p>
            </section>
        </div>
    </div>
</x-public-layout>
