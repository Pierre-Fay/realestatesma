<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <x-ui.sidebar-provider>
        <x-ui.sidebar variant="inset">
            <x-ui.sidebar-header>
                <x-ui.sidebar-menu>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button size="lg" href="{{ route('dashboard') }}">
                            <div class="bg-primary text-primary-foreground flex aspect-square size-8 items-center justify-center rounded-lg">
                                <x-lucide-building-2 class="size-4" />
                            </div>
                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ config('app.name') }}</span>
                                <span class="truncate text-xs">Luxury Real Estate</span>
                            </div>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                </x-ui.sidebar-menu>
            </x-ui.sidebar-header>

            <x-ui.sidebar-content>
                <x-ui.sidebar-group>
                    <x-ui.sidebar-group-label>{{ __('Menu') }}</x-ui.sidebar-group-label>
                    <x-ui.sidebar-menu>
                        <x-ui.sidebar-menu-item>
                            <x-ui.sidebar-menu-button href="{{ route('dashboard') }}" :is-active="request()->routeIs('dashboard')">
                                <x-lucide-layout-dashboard />
                                <span>{{ __('Dashboard') }}</span>
                            </x-ui.sidebar-menu-button>
                        </x-ui.sidebar-menu-item>

                        @if (Auth::user()->isAgent())
                            <x-ui.sidebar-menu-item>
                                <x-ui.sidebar-menu-button href="{{ route('listings.index') }}" :is-active="request()->routeIs('listings.*')">
                                    <x-lucide-house />
                                    <span>{{ __('My listings') }}</span>
                                </x-ui.sidebar-menu-button>
                            </x-ui.sidebar-menu-item>
                            <x-ui.sidebar-menu-item>
                                <x-ui.sidebar-menu-button href="{{ route('leads.index') }}" :is-active="request()->routeIs('leads.*')">
                                    <x-lucide-inbox />
                                    <span>{{ __('Leads') }}</span>
                                </x-ui.sidebar-menu-button>
                            </x-ui.sidebar-menu-item>
                        @endif

                        @if (Auth::user()->isAdmin())
                            <x-ui.sidebar-menu-item>
                                <x-ui.sidebar-menu-button href="{{ route('admin') }}" :is-active="request()->routeIs('admin')">
                                    <x-lucide-shield />
                                    <span>{{ __('Administration') }}</span>
                                </x-ui.sidebar-menu-button>
                            </x-ui.sidebar-menu-item>
                            <x-ui.sidebar-menu-item>
                                <x-ui.sidebar-menu-button href="{{ route('admin.users.index') }}" :is-active="request()->routeIs('admin.users.*')">
                                    <x-lucide-users />
                                    <span>{{ __('Users') }}</span>
                                </x-ui.sidebar-menu-button>
                            </x-ui.sidebar-menu-item>
                            <x-ui.sidebar-menu-item>
                                <x-ui.sidebar-menu-button href="{{ route('admin.agents.index') }}" :is-active="request()->routeIs('admin.agents.*')">
                                    <x-lucide-user-round />
                                    <span>{{ __('Agents') }}</span>
                                </x-ui.sidebar-menu-button>
                            </x-ui.sidebar-menu-item>
                            <x-ui.sidebar-menu-item>
                                <x-ui.sidebar-menu-button href="{{ route('admin.properties.index') }}" :is-active="request()->routeIs('admin.properties.*')">
                                    <x-lucide-building-2 />
                                    <span>{{ __('Properties') }}</span>
                                </x-ui.sidebar-menu-button>
                            </x-ui.sidebar-menu-item>
                            <x-ui.sidebar-menu-item>
                                <x-ui.sidebar-menu-button href="{{ route('admin.leads.index') }}" :is-active="request()->routeIs('admin.leads.*')">
                                    <x-lucide-inbox />
                                    <span>{{ __('All leads') }}</span>
                                </x-ui.sidebar-menu-button>
                            </x-ui.sidebar-menu-item>
                        @endif
                    </x-ui.sidebar-menu>
                </x-ui.sidebar-group>
            </x-ui.sidebar-content>

            <x-ui.sidebar-footer>
                @php
                    $user = Auth::user();
                @endphp
                <x-ui.sidebar-menu>
                    <x-ui.sidebar-menu-item>
                        <x-ui.dropdown-menu>
                            <x-ui.dropdown-menu-trigger class="w-full">
                                <x-ui.sidebar-menu-button
                                    size="lg"
                                    class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                                    ::data-state="open ? 'open' : 'closed'"
                                >
                                    <x-ui.avatar class="h-8 w-8 rounded-lg">
                                        <x-ui.avatar-fallback class="rounded-lg">{{ $user->initials }}</x-ui.avatar-fallback>
                                    </x-ui.avatar>
                                    <div class="grid flex-1 text-left text-sm leading-tight">
                                        <span class="truncate font-medium">{{ $user->name }}</span>
                                        <span class="truncate text-xs">{{ $user->email }}</span>
                                    </div>
                                    <x-lucide-chevrons-up-down class="ml-auto size-4" />
                                </x-ui.sidebar-menu-button>
                            </x-ui.dropdown-menu-trigger>

                            <x-ui.dropdown-menu-content class="w-(--radix-dropdown-menu-trigger-width) min-w-56 rounded-lg" side="right" align="end" :side-offset="4">
                                <x-ui.dropdown-menu-item :href="route('profile.edit')">
                                    <x-lucide-user />
                                        {{ __('Account settings') }}
                                </x-ui.dropdown-menu-item>
                                <x-ui.dropdown-menu-separator />
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-ui.dropdown-menu-item type="submit">
                                        <x-lucide-log-out />
                                        {{ __('Log Out') }}
                                    </x-ui.dropdown-menu-item>
                                </form>
                            </x-ui.dropdown-menu-content>
                        </x-ui.dropdown-menu>
                    </x-ui.sidebar-menu-item>
                </x-ui.sidebar-menu>
            </x-ui.sidebar-footer>
        </x-ui.sidebar>

        <x-ui.sidebar-inset>
            <header class="flex h-16 shrink-0 items-center gap-2 border-b px-4 lg:px-6">
                <x-ui.sidebar-trigger class="-ml-1" />
                <x-ui.separator orientation="vertical" class="mr-2 data-[orientation=vertical]:h-4" />
                <h1 class="text-base font-medium">{{ $header ?? config('app.name') }}</h1>
            </header>

            <div class="flex flex-1 flex-col gap-4 p-4 md:gap-6 md:p-6">
                {{ $slot }}
            </div>
        </x-ui.sidebar-inset>
    </x-ui.sidebar-provider>
</body>
</html>
