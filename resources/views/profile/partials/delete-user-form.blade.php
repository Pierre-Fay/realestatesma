<x-ui.card variant="sectioned" class="border-destructive/30">
    <x-ui.card-header>
        <x-ui.card-title><h3>{{ __('Delete account') }}</h3></x-ui.card-title>
        <x-ui.card-description>{{ __('Permanently delete your login account. This action cannot be undone.') }}</x-ui.card-description>
    </x-ui.card-header>

    <x-ui.card-content>
        <x-ui.alert-dialog :open="$errors->userDeletion->isNotEmpty()">
            <x-ui.alert-dialog-trigger>
                <x-ui.button type="button" variant="destructive">{{ __('Delete account') }}</x-ui.button>
            </x-ui.alert-dialog-trigger>

            <x-ui.alert-dialog-content x-effect="if (open) $nextTick(() => $el.querySelector('input[name=password]')?.focus())">
                <x-ui.alert-dialog-header>
                    <x-ui.alert-dialog-title>{{ __('Are you sure you want to delete your account?') }}</x-ui.alert-dialog-title>
                    <x-ui.alert-dialog-description>{{ __('Please enter your current password to confirm permanent deletion of your login account.') }}</x-ui.alert-dialog-description>
                </x-ui.alert-dialog-header>

                <form method="POST" action="{{ route('profile.destroy') }}" class="flex flex-col gap-4">
                    @csrf
                    @method('DELETE')

                    @if ($errors->userDeletion->has('delete'))
                        <x-ui.alert tone="warning">
                            <x-ui.alert-title>{{ $errors->userDeletion->first('delete') }}</x-ui.alert-title>
                        </x-ui.alert>
                    @endif

                    <x-ui.field>
                        <x-ui.field-label for="delete-account-password">{{ __('Current password') }}</x-ui.field-label>
                        <x-ui.input
                            id="delete-account-password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            :aria-invalid="$errors->userDeletion->has('password') ? 'true' : 'false'"
                            :aria-describedby="$errors->userDeletion->has('password') ? 'delete-account-password-error' : null"
                        />
                        <x-ui.field-error id="delete-account-password-error" :messages="$errors->userDeletion->get('password')" />
                    </x-ui.field>

                    <x-ui.alert-dialog-footer>
                        <x-ui.alert-dialog-cancel>{{ __('Cancel') }}</x-ui.alert-dialog-cancel>
                        <x-ui.button type="submit" variant="destructive">{{ __('Delete account') }}</x-ui.button>
                    </x-ui.alert-dialog-footer>
                </form>
            </x-ui.alert-dialog-content>
        </x-ui.alert-dialog>
    </x-ui.card-content>
</x-ui.card>
