<x-guest-layout>
    <x-ui.card variant="sectioned">
        <x-ui.card-header>
            <x-ui.card-title class="text-lg">{{ __('Sign in') }}</x-ui.card-title>
        </x-ui.card-header>

        <x-ui.card-content>
            @if (session('status'))
                <x-ui.alert tone="success" class="mb-4">
                    <x-lucide-circle-check class="size-4" />
                    <x-ui.alert-description>{{ session('status') }}</x-ui.alert-description>
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4" novalidate>
                @csrf

                <x-ui.field>
                    <x-ui.field-label for="email">{{ __('Email') }}</x-ui.field-label>
                    <x-ui.input
                        id="email"
                        name="email"
                        type="email"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <x-ui.field-error :messages="$errors->get('email')" />
                </x-ui.field>

                <x-ui.field>
                    <x-ui.field-label for="password">{{ __('Password') }}</x-ui.field-label>
                    <x-ui.input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                    />
                    <x-ui.field-error :messages="$errors->get('password')" />
                </x-ui.field>

                <div class="flex items-center justify-between gap-4">
                    <label class="flex items-center gap-2 text-sm">
                        <x-ui.checkbox name="remember" :checked="(bool) old('remember')" />
                        {{ __('Remember me') }}
                    </label>

                    @if (Route::has('password.request'))
                        <x-ui.link :href="route('password.request')" variant="muted" class="text-sm">
                            {{ __('Forgot your password?') }}
                        </x-ui.link>
                    @endif
                </div>

                <x-ui.button type="submit" class="w-full">{{ __('Sign in') }}</x-ui.button>
            </form>
        </x-ui.card-content>
    </x-ui.card>
</x-guest-layout>
