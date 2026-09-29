<x-app-layout>
    <x-slot name="header">{{ __('New agent') }}</x-slot>

    <div class="max-w-4xl">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>{{ __('Create an agent account') }}</x-ui.card-title>
                <x-ui.card-description>
                    {{ __('Creates both the login and the linked public profile.') }}
                </x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                <form method="POST" action="{{ route('admin.agents.store') }}" class="flex flex-col gap-6">
                    @csrf

                    <x-ui.field>
                        <x-ui.field-label for="name">{{ __('Full name') }}</x-ui.field-label>
                        <x-ui.input id="name" name="name" type="text" :value="old('name')" required autofocus />
                        <x-ui.field-error>{{ $errors->first('name') }}</x-ui.field-error>
                    </x-ui.field>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <x-ui.field>
                            <x-ui.field-label for="login_email">{{ __('Login email') }}</x-ui.field-label>
                            <x-ui.input id="login_email" name="login_email" type="email" :value="old('login_email')" required />
                            <x-ui.field-description>{{ __('Private — used to sign in; never shown publicly.') }}</x-ui.field-description>
                            <x-ui.field-error>{{ $errors->first('login_email') }}</x-ui.field-error>
                        </x-ui.field>

                        <x-ui.field>
                            <x-ui.field-label for="public_email">{{ __('Public email') }}</x-ui.field-label>
                            <x-ui.input id="public_email" name="public_email" type="email" :value="old('public_email')" required />
                            <x-ui.field-description>{{ __('Displayed on the agent public profile.') }}</x-ui.field-description>
                            <x-ui.field-error>{{ $errors->first('public_email') }}</x-ui.field-error>
                        </x-ui.field>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <x-ui.field>
                            <x-ui.field-label for="password">{{ __('Password') }}</x-ui.field-label>
                            <x-ui.input id="password" name="password" type="password" required />
                            <x-ui.field-error>{{ $errors->first('password') }}</x-ui.field-error>
                        </x-ui.field>

                        <x-ui.field>
                            <x-ui.field-label for="password_confirmation">{{ __('Confirm password') }}</x-ui.field-label>
                            <x-ui.input id="password_confirmation" name="password_confirmation" type="password" required />
                        </x-ui.field>
                    </div>

                    <x-ui.field>
                        <x-ui.field-label for="phone">{{ __('Phone') }}</x-ui.field-label>
                        <x-ui.input id="phone" name="phone" type="tel" :value="old('phone')" />
                        <x-ui.field-error>{{ $errors->first('phone') }}</x-ui.field-error>
                    </x-ui.field>

                    <x-ui.field>
                        <x-ui.field-label for="bio">{{ __('Bio') }}</x-ui.field-label>
                        <x-ui.textarea id="bio" name="bio" rows="4">{{ old('bio') }}</x-ui.textarea>
                        <x-ui.field-error>{{ $errors->first('bio') }}</x-ui.field-error>
                    </x-ui.field>

                    <x-ui.field>
                        <label class="flex items-center gap-2 text-sm font-medium select-none">
                            <input type="hidden" name="is_active" value="0" />
                            <input
                                id="is_active"
                                name="is_active"
                                type="checkbox"
                                value="1"
                                @checked(old('is_active', true))
                                class="border-input accent-primary size-4 shrink-0 rounded-[4px] border shadow-xs"
                            />
                            {{ __('Public profile visible') }}
                        </label>
                        <x-ui.field-description>{{ __('Inactive agents keep their listings but are hidden from the public directory.') }}</x-ui.field-description>
                    </x-ui.field>

                    <div class="flex items-center gap-2">
                        <x-ui.button type="submit">{{ __('Create agent') }}</x-ui.button>
                        <x-ui.button :href="route('admin.agents.index')" variant="ghost">{{ __('Cancel') }}</x-ui.button>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>
</x-app-layout>
