<x-app-layout>
    <x-slot name="header">{{ __('Agents') }}</x-slot>

    <div class="flex flex-col gap-4">
        @if (session('status'))
            <x-ui.alert tone="success">
                <x-lucide-circle-check />
                <x-ui.alert-title>{{ __('Agent created.') }}</x-ui.alert-title>
            </x-ui.alert>
        @endif

        <div class="flex items-center justify-between gap-4">
            <p class="text-muted-foreground text-sm">
                {{ __('Agents with a public profile and a login account.') }}
            </p>
            <x-ui.button :href="route('admin.agents.create')" size="sm">
                <x-lucide-plus />
                {{ __('New agent') }}
            </x-ui.button>
        </div>

        <x-ui.table variant="card">
            <x-ui.table-header>
                <x-ui.table-row>
                    <x-ui.table-head>{{ __('Name') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Public email') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Phone') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Profile') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Login') }}</x-ui.table-head>
                </x-ui.table-row>
            </x-ui.table-header>
            <x-ui.table-body>
                @forelse ($agents as $agent)
                    <x-ui.table-row>
                        <x-ui.table-cell>
                            <div class="flex items-center gap-2">
                                <x-ui.avatar class="size-8 rounded-lg">
                                    @if ($agent->photo)
                                        <x-ui.avatar-image :src="$agent->photo" :alt="$agent->name" />
                                    @endif
                                    <x-ui.avatar-fallback class="rounded-lg">
                                        <x-lucide-user-round class="size-4" />
                                    </x-ui.avatar-fallback>
                                </x-ui.avatar>
                                <span class="font-medium">{{ $agent->name }}</span>
                            </div>
                        </x-ui.table-cell>
                        <x-ui.table-cell class="text-muted-foreground">{{ $agent->email }}</x-ui.table-cell>
                        <x-ui.table-cell class="text-muted-foreground">{{ $agent->phone ?? '—' }}</x-ui.table-cell>
                        <x-ui.table-cell>
                            @if ($agent->is_active)
                                <x-ui.badge tone="success">{{ __('Active') }}</x-ui.badge>
                            @else
                                <x-ui.badge tone="neutral">{{ __('Inactive') }}</x-ui.badge>
                            @endif
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            @if ($agent->user)
                                <span class="text-muted-foreground">{{ $agent->user->email }}</span>
                            @else
                                <x-ui.badge tone="neutral" variant="outline">{{ __('No login') }}</x-ui.badge>
                            @endif
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @empty
                    <x-ui.table-row>
                        <x-ui.table-cell colspan="5" class="text-muted-foreground py-6 text-center">
                            {{ __('No agents yet.') }}
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @endforelse
            </x-ui.table-body>
        </x-ui.table>

        <x-paginator :paginator="$agents" />
    </div>
</x-app-layout>
