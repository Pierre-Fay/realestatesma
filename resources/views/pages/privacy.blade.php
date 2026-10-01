<x-public-layout :title="__('Privacy policy')">
    <div class="mx-auto flex max-w-3xl flex-col gap-6">
        <header class="flex flex-col gap-2">
            <h1 class="text-3xl font-semibold tracking-tight">{{ __('Privacy policy') }}</h1>
            <p class="text-muted-foreground">{{ __('Last updated: :date', ['date' => now()->isoFormat('MMMM YYYY')]) }}</p>
        </header>

        <div class="text-muted-foreground flex flex-col gap-6 leading-relaxed">
            <section class="flex flex-col gap-2">
                <h2 class="text-foreground text-lg font-semibold">{{ __('Who we are') }}</h2>
                <p>
                    {{ __('Real Estate SMA is the data controller for the personal data collected through this website. You can reach us at :email.', ['email' => 'contact@realestatesma.test']) }}
                </p>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-foreground text-lg font-semibold">{{ __('What we collect') }}</h2>
                <ul class="ms-5 list-disc">
                    <li>{{ __('Your name, email address and, if you provide it, your phone number.') }}</li>
                    <li>{{ __('The message you send us and the property you are interested in.') }}</li>
                    <li>{{ __('Your explicit consent to be contacted and to the processing of your data.') }}</li>
                </ul>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-foreground text-lg font-semibold">{{ __('Why we collect it') }}</h2>
                <p>
                    {{ __('We use your data only to respond to your enquiry, to suggest matching properties and to stay in touch about your search. We do not sell your personal data.') }}
                </p>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-foreground text-lg font-semibold">{{ __('Legal basis') }}</h2>
                <p>
                    {{ __('We process your data on the basis of the consent you give when you submit an enquiry or contact request. You may withdraw that consent at any time.') }}
                </p>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-foreground text-lg font-semibold">{{ __('Retention') }}</h2>
                <p>
                    {{ __('We keep your data for as long as needed to handle your request and to comply with our legal obligations, then delete it.') }}
                </p>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-foreground text-lg font-semibold">{{ __('Your rights') }}</h2>
                <p>
                    {{ __('You have the right to access, correct, delete or restrict the processing of your data, to object to it, and to data portability. To exercise any of these rights, contact us at :email.', ['email' => 'contact@realestatesma.test']) }}
                </p>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-foreground text-lg font-semibold">{{ __('Cookies') }}</h2>
                <p>
                    {{ __('We use only the cookies required for the website to work (session and security). We do not use advertising or third-party tracking cookies.') }}
                </p>
            </section>
        </div>
    </div>
</x-public-layout>
