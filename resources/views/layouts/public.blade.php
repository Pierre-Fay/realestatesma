<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ $title ?? config('app.name', 'Real Estate SMA') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-foreground font-sans antialiased">
    <header class="border-b">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8">
            <a href="/" class="flex items-center gap-2 font-semibold">
                <x-lucide-building-2 class="size-5" />
                {{ config('app.name', 'Real Estate SMA') }}
            </a>

            <nav class="flex items-center gap-6 text-sm">
                <a
                    href="{{ route('properties.index') }}"
                    class="text-muted-foreground hover:text-foreground font-medium transition-colors"
                >
                    {{ __('Properties') }}
                </a>
                <a
                    href="{{ route('contact') }}"
                    class="text-muted-foreground hover:text-foreground font-medium transition-colors"
                >
                    {{ __('Contact') }}
                </a>
            </nav>
        </div>
    </header>

    <main class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

    <footer class="border-t">
        <div class="text-muted-foreground mx-auto max-w-6xl px-4 py-8 text-sm sm:px-6 lg:px-8">
            {{ config('app.name', 'Real Estate SMA') }} — {{ __('Luxury real estate in San Miguel de Allende, Mexico.') }}
        </div>
    </footer>
</body>
</html>
