<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config('app.name', 'Real Estate SMA') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap"
            rel="stylesheet"
        />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-background text-foreground font-sans antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center gap-6 p-6">
            <a href="/" class="text-2xl font-semibold tracking-tight">
                {{ config('app.name', 'Real Estate SMA') }}
            </a>

            <div class="w-full sm:max-w-md">{{ $slot }}</div>
        </div>
    </body>
</html>
