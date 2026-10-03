@php
    $user = Auth::user();
    $shortcuts = $user->isAdmin() ? [
        ['route' => 'admin.leads.index', 'title' => __('All leads'), 'description' => __('Review contact requests and manage lead assignments.'), 'icon' => 'lucide-inbox'],
        ['route' => 'admin.properties.index', 'title' => __('Properties'), 'description' => __('Review listings and manage their publication.'), 'icon' => 'lucide-house'],
        ['route' => 'admin.agents.index', 'title' => __('Agents'), 'description' => __('Browse agent profiles and create agent accounts.'), 'icon' => 'lucide-users'],
        ['route' => 'admin.users.index', 'title' => __('Users'), 'description' => __('Manage who can sign in to the back office.'), 'icon' => 'lucide-user-cog'],
        ['route' => 'admin', 'title' => __('Open administration'), 'description' => __('Access the administration area.'), 'icon' => 'lucide-shield'],
    ] : [
        ['route' => 'leads.index', 'title' => __('Leads'), 'description' => __('Follow up on your assigned leads and track their progress.'), 'icon' => 'lucide-inbox'],
        ['route' => 'listings.index', 'title' => __('My listings'), 'description' => __('Review and update your property listings.'), 'icon' => 'lucide-house'],
        ['route' => 'listings.create', 'title' => __('Create listing'), 'description' => __('Add a property to your portfolio for review.'), 'icon' => 'lucide-circle-plus'],
    ];

    $shortcuts[] = ['route' => 'profile.edit', 'title' => __('Account settings'), 'description' => __('Update your login details and password.'), 'icon' => 'lucide-settings'];
@endphp

<x-app-layout>
    <x-slot name="header">{{ __('Dashboard') }}</x-slot>

    <x-ui.card variant="sectioned">
        <x-ui.card-header>
            <x-ui.card-title class="text-xl leading-tight"><h2>{{ __('Welcome, :name', ['name' => $user->name]) }}</h2></x-ui.card-title>
            <x-ui.card-description>
                @if ($user->isAdmin())
                    {{ __('You are signed in as an Administrator.') }}
                @else
                    {{ __('You are signed in as an Agent.') }}
                @endif
            </x-ui.card-description>
        </x-ui.card-header>
    </x-ui.card>

    <section aria-labelledby="quick-access-heading" class="flex flex-col gap-4">
        <div class="flex flex-col gap-1">
            <h2 id="quick-access-heading" class="text-lg font-semibold">{{ __('Quick access') }}</h2>
            <p class="text-muted-foreground text-sm">{{ __('Choose a workspace to get started.') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($shortcuts as $shortcut)
                <x-dashboard-shortcut
                    :href="route($shortcut['route'])"
                    :title="$shortcut['title']"
                    :description="$shortcut['description']"
                    :icon="$shortcut['icon']"
                />
            @endforeach
        </div>
    </section>
</x-app-layout>
