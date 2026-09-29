<x-app-layout>
    <x-slot name="header">{{ __('Administration') }}</x-slot>

    <x-ui.card>
        <x-ui.card-header>
            <x-ui.card-title>{{ __('Administration') }}</x-ui.card-title>
            <x-ui.card-description>{{ __('Admin-only area, restricted to administrators.') }}</x-ui.card-description>
        </x-ui.card-header>
        <x-ui.card-content>
            <x-ui.button :href="route('admin.users.index')" variant="outline">{{ __('Manage users') }}</x-ui.button>
        </x-ui.card-content>
    </x-ui.card>
</x-app-layout>
