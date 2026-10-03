<x-app-layout>
    <x-slot name="header">{{ __('Account settings') }}</x-slot>

    <div class="flex w-full max-w-3xl flex-col gap-4 md:gap-6">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-semibold tracking-tight">{{ __('Manage your account') }}</h2>
            <p class="text-muted-foreground text-sm">{{ __('Update your login details and password.') }}</p>
        </div>

        @include('profile.partials.update-profile-information-form')
        @include('profile.partials.update-password-form')
        @include('profile.partials.delete-user-form')
    </div>
</x-app-layout>
