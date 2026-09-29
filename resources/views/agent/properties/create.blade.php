<x-app-layout>
    <x-slot name="header">{{ __('New listing') }}</x-slot>

    <div class="max-w-4xl">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>{{ __('Create a listing') }}</x-ui.card-title>
                <x-ui.card-description>{{ __('New listings are reviewed by an admin before going live.') }}</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                @include('agent.properties._form', [
                    'property' => null,
                    'categories' => $categories,
                    'action' => route('listings.store'),
                    'method' => 'POST',
                    'submit' => __('Create listing'),
                ])
            </x-ui.card-content>
        </x-ui.card>
    </div>
</x-app-layout>
