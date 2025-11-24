<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Iconify Icons (200k+ icons from all libraries) -->
    <script defer src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>

    <!-- Turbo Drive for smooth, no-hard-refresh navigation -->
    <script defer src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            if (window.Turbo) {
                Turbo.session.drive = true;
            }
        });
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-sacli-grey-200">
        <x-unified-navigation />

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow-sm border-b border-sacli-green-200">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <x-flash-message type="success">
                    {{ session('success') }}
                </x-flash-message>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <x-flash-message type="error">
                    {{ session('error') }}
                </x-flash-message>
            </div>
        @endif

        @if (session('warning'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <x-flash-message type="warning">
                    {{ session('warning') }}
                </x-flash-message>
            </div>
        @endif

        @if (session('info'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <x-flash-message type="info">
                    {{ session('info') }}
                </x-flash-message>
            </div>
        @endif

        <!-- Page Content -->
        <main id="main-content" role="main">
            {{ $slot }}
        </main>
    </div>

    <!-- Chat Support Widget -->
    <x-chat-widget />

    <!-- Additional Scripts -->
    @stack('scripts')
</body>

</html>
