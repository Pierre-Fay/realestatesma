<x-public-layout :title="__('Contact')">
    <div class="mx-auto flex max-w-2xl flex-col gap-6">
        <header>
            <h1 class="text-2xl font-semibold tracking-tight">{{ __('Contact us') }}</h1>
            <p class="text-muted-foreground text-sm">
                {{ __('Tell us what you are looking for and we will get in touch with matching properties.') }}
            </p>
        </header>

        <x-ui.card>
            <div class="flex flex-col gap-4">
                @if (session('status') === 'lead-sent')
                    <x-ui.alert tone="success">
                        <x-lucide-circle-check />
                        <x-ui.alert-title>{{ __('Thanks! We will be in touch shortly.') }}</x-ui.alert-title>
                    </x-ui.alert>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="flex flex-col gap-4">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <x-ui.field>
                            <x-ui.field-label for="first_name">{{ __('First name') }}</x-ui.field-label>
                            <x-ui.input id="first_name" name="first_name" type="text" :value="old('first_name')" required />
                            <x-ui.field-error>{{ $errors->first('first_name') }}</x-ui.field-error>
                        </x-ui.field>

                        <x-ui.field>
                            <x-ui.field-label for="last_name">{{ __('Last name') }}</x-ui.field-label>
                            <x-ui.input id="last_name" name="last_name" type="text" :value="old('last_name')" required />
                            <x-ui.field-error>{{ $errors->first('last_name') }}</x-ui.field-error>
                        </x-ui.field>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
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
                    </div>

                    <x-ui.field>
                        <x-ui.field-label for="interested_in">{{ __('Interested in') }}</x-ui.field-label>
                        <x-ui.textarea id="interested_in" name="interested_in" rows="3" required placeholder="{{ __('e.g. a 3-bedroom villa in Centro, ready to move in') }}">{{ old('interested_in') }}</x-ui.textarea>
                        <x-ui.field-error>{{ $errors->first('interested_in') }}</x-ui.field-error>
                    </x-ui.field>

                    <x-ui.field>
                        <x-ui.field-label for="budget">{{ __('Budget (USD)') }}</x-ui.field-label>
                        <x-ui.input id="budget" name="budget" type="number" min="0" step="1000" :value="old('budget')" />
                        <x-ui.field-error>{{ $errors->first('budget') }}</x-ui.field-error>
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

                    <x-ui.button type="submit" class="sm:self-start">{{ __('Send') }}</x-ui.button>
                </form>
            </div>
        </x-ui.card>
    </div>
</x-public-layout>
