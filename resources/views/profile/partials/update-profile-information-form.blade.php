<x-ui.card variant="sectioned">
    <x-ui.card-header>
        <x-ui.card-title><h3>{{ __('Account information') }}</h3></x-ui.card-title>
        <x-ui.card-description>{{ __("Update your account's name and login email address.") }}</x-ui.card-description>
    </x-ui.card-header>

    <x-ui.card-content class="flex flex-col gap-4">
        @if (session('status') === 'profile-updated')
            <x-ui.alert tone="success">
                <x-lucide-circle-check />
                <x-ui.alert-title>{{ __('Account information saved.') }}</x-ui.alert-title>
            </x-ui.alert>
        @endif

        <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="POST" action="{{ route('profile.update') }}" class="flex flex-col gap-4">
            @csrf
            @method('PATCH')

            <x-ui.field>
                <x-ui.field-label for="name">{{ __('Name') }}</x-ui.field-label>
                <x-ui.input
                    id="name"
                    name="name"
                    type="text"
                    :value="old('name', $user->name)"
                    required
                    autofocus
                    autocomplete="name"
                    :aria-invalid="$errors->has('name') ? 'true' : 'false'"
                    :aria-describedby="$errors->has('name') ? 'name-error' : null"
                />
                <x-ui.field-error id="name-error" :messages="$errors->get('name')" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="email">{{ __('Login email') }}</x-ui.field-label>
                <x-ui.input
                    id="email"
                    name="email"
                    type="email"
                    :value="old('email', $user->email)"
                    required
                    autocomplete="username"
                    :aria-invalid="$errors->has('email') ? 'true' : 'false'"
                    :aria-describedby="$errors->has('email') ? 'email-error' : null"
                />
                <x-ui.field-error id="email-error" :messages="$errors->get('email')" />
            </x-ui.field>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <x-ui.alert tone="warning">
                    <x-ui.alert-title>{{ __('Your email address is unverified.') }}</x-ui.alert-title>
                    <x-ui.alert-description>
                        <x-ui.button type="submit" form="send-verification" variant="link" class="h-auto whitespace-normal p-0 text-start">
                            {{ __('Click here to re-send the verification email.') }}
                        </x-ui.button>
                    </x-ui.alert-description>
                </x-ui.alert>
            @endif

            @if (session('status') === 'verification-link-sent')
                <x-ui.alert tone="success">
                    <x-ui.alert-title>{{ __('A new verification link has been sent to your email address.') }}</x-ui.alert-title>
                </x-ui.alert>
            @endif

            <div>
                <x-ui.button type="submit">{{ __('Save account information') }}</x-ui.button>
            </div>
        </form>
    </x-ui.card-content>
</x-ui.card>
