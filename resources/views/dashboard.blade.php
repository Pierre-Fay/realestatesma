<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>{{ __('Welcome, :name', ['name' => Auth::user()->name]) }}</x-ui.card-title>
                    <x-ui.card-description>
                        @if (Auth::user()->isAdmin())
                            {{ __('You are signed in as an Administrator.') }}
                        @else
                            {{ __('You are signed in as an Agent.') }}
                        @endif
                    </x-ui.card-description>
                </x-ui.card-header>

                @if (Auth::user()->isAdmin())
                    <x-ui.card-content>
                        <x-ui.button :href="route('admin')" variant="outline">{{ __('Open administration') }}</x-ui.button>
                    </x-ui.card-content>
                @endif
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
