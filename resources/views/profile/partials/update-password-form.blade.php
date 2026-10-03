<x-ui.card variant="sectioned">
    <x-ui.card-header>
        <x-ui.card-title><h3>{{ __('Update password') }}</h3></x-ui.card-title>
        <x-ui.card-description>{{ __('Ensure your account is using a long, random password to stay secure.') }}</x-ui.card-description>
    </x-ui.card-header>

    <x-ui.card-content class="flex flex-col gap-4">
        @if (session('status') === 'password-updated')
            <x-ui.alert tone="success">
                <x-lucide-circle-check />
                <x-ui.alert-title>{{ __('Password updated.') }}</x-ui.alert-title>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-4">
            @csrf
            @method('PUT')

            <x-ui.field>
                <x-ui.field-label for="update_password_current_password">{{ __('Current password') }}</x-ui.field-label>
                <x-ui.input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    required
                    autocomplete="current-password"
                    :aria-invalid="$errors->updatePassword->has('current_password') ? 'true' : 'false'"
                    :aria-describedby="$errors->updatePassword->has('current_password') ? 'current-password-error' : null"
                />
                <x-ui.field-error id="current-password-error" :messages="$errors->updatePassword->get('current_password')" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="update_password_password">{{ __('New password') }}</x-ui.field-label>
                <x-ui.input
                    id="update_password_password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    :aria-invalid="$errors->updatePassword->has('password') ? 'true' : 'false'"
                    :aria-describedby="$errors->updatePassword->has('password') ? 'new-password-error' : null"
                />
                <x-ui.field-error id="new-password-error" :messages="$errors->updatePassword->get('password')" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="update_password_password_confirmation">{{ __('Confirm password') }}</x-ui.field-label>
                <x-ui.input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    :aria-invalid="$errors->updatePassword->has('password_confirmation') ? 'true' : 'false'"
                    :aria-describedby="$errors->updatePassword->has('password_confirmation') ? 'password-confirmation-error' : null"
                />
                <x-ui.field-error id="password-confirmation-error" :messages="$errors->updatePassword->get('password_confirmation')" />
            </x-ui.field>

            <div>
                <x-ui.button type="submit">{{ __('Update password') }}</x-ui.button>
            </div>
        </form>
    </x-ui.card-content>
</x-ui.card>
