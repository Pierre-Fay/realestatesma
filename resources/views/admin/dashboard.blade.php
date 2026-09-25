<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">{{ __('Administration') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>{{ __('Administration') }}</x-ui.card-title>
                    <x-ui.card-description>{{ __('Admin-only area, restricted to administrators.') }}</x-ui.card-description>
                </x-ui.card-header>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
