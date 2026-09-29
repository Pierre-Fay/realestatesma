<x-app-layout>
    <x-slot name="header">{{ __('Users') }}</x-slot>

    <div class="flex flex-col gap-4">
        @if (session('status'))
            <x-ui.alert tone="success">
                <x-lucide-circle-check />
                <x-ui.alert-title>
                    {{ session('status') === 'user-enabled' ? __('User enabled.') : __('User disabled.') }}
                </x-ui.alert-title>
            </x-ui.alert>
        @endif

        <p class="text-muted-foreground text-sm">
            {{ __('Enable or disable a user\'s login access. Disabling revokes access immediately.') }}
        </p>

        <x-ui.table variant="card">
            <x-ui.table-header>
                <x-ui.table-row>
                    <x-ui.table-head>{{ __('Name') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Email') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Role') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Status') }}</x-ui.table-head>
                    <x-ui.table-head class="text-end">{{ __('Actions') }}</x-ui.table-head>
                </x-ui.table-row>
            </x-ui.table-header>
            <x-ui.table-body>
                @forelse ($users as $user)
                    <x-ui.table-row>
                        <x-ui.table-cell>
                            <div class="flex items-center gap-2">
                                <x-ui.avatar class="size-8 rounded-lg">
                                    <x-ui.avatar-fallback class="rounded-lg">{{ $user->initials }}</x-ui.avatar-fallback>
                                </x-ui.avatar>
                                <span class="font-medium">{{ $user->name }}</span>
                                @if ($user->is(auth()->user()))
                                    <x-ui.badge tone="neutral" variant="outline">{{ __('You') }}</x-ui.badge>
                                @endif
                            </div>
                        </x-ui.table-cell>
                        <x-ui.table-cell class="text-muted-foreground">{{ $user->email }}</x-ui.table-cell>
                        <x-ui.table-cell>
                            @if ($user->isAdmin())
                                <x-ui.badge>{{ __('Admin') }}</x-ui.badge>
                            @else
                                <x-ui.badge variant="secondary">{{ __('Agent') }}</x-ui.badge>
                            @endif
                        </x-ui.table-cell>
                        <x-ui.table-cell>
                            @if ($user->is_enabled)
                                <x-ui.badge tone="success">{{ __('Enabled') }}</x-ui.badge>
                            @else
                                <x-ui.badge tone="danger">{{ __('Disabled') }}</x-ui.badge>
                            @endif
                        </x-ui.table-cell>
                        <x-ui.table-cell class="text-end">
                            @if ($user->is_enabled)
                                @can('disable', $user)
                                    <x-ui.alert-dialog>
                                        <x-ui.alert-dialog-trigger>
                                            <x-ui.button variant="destructive" size="sm" type="button">{{ __('Disable') }}</x-ui.button>
                                        </x-ui.alert-dialog-trigger>
                                        <x-ui.alert-dialog-content>
                                            <x-ui.alert-dialog-header>
                                                <x-ui.alert-dialog-title>{{ __('Disable :name?', ['name' => $user->name]) }}</x-ui.alert-dialog-title>
                                                <x-ui.alert-dialog-description>
                                                    {{ __('They will be signed out immediately and will not be able to sign in again until re-enabled.') }}
                                                </x-ui.alert-dialog-description>
                                            </x-ui.alert-dialog-header>
                                            <x-ui.alert-dialog-footer>
                                                <x-ui.alert-dialog-cancel>{{ __('Cancel') }}</x-ui.alert-dialog-cancel>
                                                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="is_enabled" value="0" />
                                                    <x-ui.alert-dialog-action x-on:click="$el.closest('form').submit()">
                                                        {{ __('Disable') }}
                                                    </x-ui.alert-dialog-action>
                                                </form>
                                            </x-ui.alert-dialog-footer>
                                        </x-ui.alert-dialog-content>
                                    </x-ui.alert-dialog>
                                @else
                                    <x-ui.tooltip>
                                        <x-ui.tooltip-trigger>
                                            <x-ui.button variant="ghost" size="sm" type="button" disabled class="cursor-not-allowed">{{ __('Disable') }}</x-ui.button>
                                        </x-ui.tooltip-trigger>
                                        <x-ui.tooltip-content>
                                            {{ $user->is(auth()->user()) ? __('You cannot disable your own account.') : __('You cannot disable the last active administrator.') }}
                                        </x-ui.tooltip-content>
                                    </x-ui.tooltip>
                                @endcan
                            @else
                                @can('enable', $user)
                                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_enabled" value="1" />
                                        <x-ui.button variant="outline" size="sm" type="submit">{{ __('Enable') }}</x-ui.button>
                                    </form>
                                @endcan
                            @endif
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @empty
                    <x-ui.table-row>
                        <x-ui.table-cell colspan="5" class="text-muted-foreground py-6 text-center">
                            {{ __('No users found.') }}
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @endforelse
            </x-ui.table-body>
        </x-ui.table>

        <x-paginator :paginator="$users" />
    </div>
</x-app-layout>
