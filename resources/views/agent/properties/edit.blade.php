<x-app-layout>
    <x-slot name="header">{{ __('Edit listing') }}</x-slot>

    <div class="max-w-4xl">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>{{ $property->name }}</x-ui.card-title>
                <x-ui.card-description>{{ __('Update your listing details, photos and categories.') }}</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                @include('agent.properties._form', [
                    'property' => $property,
                    'categories' => $categories,
                    'action' => route('listings.update', $property),
                    'method' => 'PUT',
                    'submit' => __('Save changes'),
                ])
            </x-ui.card-content>
        </x-ui.card>
    </div>
</x-app-layout>
