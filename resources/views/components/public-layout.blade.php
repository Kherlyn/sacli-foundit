<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'SACLI FOUNDIT' }} - {{ config('app.name', 'SACLI FOUNDIT') }}</title>

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

<body class="font-sans antialiased bg-white">
    <x-unified-navigation />

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo and Description -->
                <div class="md:col-span-2">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-sacli-green-400 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-white font-bold text-lg">S</span>
                        </div>
                        <span class="text-xl font-bold text-gray-900">SACLI FOUNDIT</span>
                    </div>
                    <p class="text-gray-600 mb-4 max-w-md">
                        Helping reunite people with their belongings through our comprehensive lost and found platform.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="text-gray-400 hover:text-sacli-green-400 transition duration-150 ease-in-out">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="text-gray-400 hover:text-sacli-green-400 transition duration-150 ease-in-out">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-4">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('home') }}"
                                class="text-gray-600 hover:text-sacli-green-400 transition duration-150 ease-in-out">Home</a>
                        </li>
                        <li><a href="{{ route('search') }}"
                                class="text-gray-600 hover:text-sacli-green-400 transition duration-150 ease-in-out">Search
                                Items</a></li>
                        <li><a href="{{ route('browse') }}"
                                class="text-gray-600 hover:text-sacli-green-400 transition duration-150 ease-in-out">Browse
                                Categories</a></li>
                        @auth
                            <li><a href="{{ route('dashboard') }}"
                                    class="text-gray-600 hover:text-sacli-green-400 transition duration-150 ease-in-out">My
                                    Dashboard</a></li>
                        @else
                            <li><a href="{{ route('register') }}"
                                    class="text-gray-600 hover:text-sacli-green-400 transition duration-150 ease-in-out">Register</a>
                            </li>
                        @endauth
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-4">Support</h3>
                    <ul class="space-y-3">
                        <li><a href="#"
                                class="text-gray-600 hover:text-sacli-green-400 transition duration-150 ease-in-out">Help
                                Center</a></li>
                        <li><a href="#"
                                class="text-gray-600 hover:text-sacli-green-400 transition duration-150 ease-in-out">Contact
                                Us</a></li>
                        <li><a href="#"
                                class="text-gray-600 hover:text-sacli-green-400 transition duration-150 ease-in-out">Privacy
                                Policy</a></li>
                        <li><a href="#"
                                class="text-gray-600 hover:text-sacli-green-400 transition duration-150 ease-in-out">Terms
                                of Service</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-gray-200">
                <p class="text-center text-gray-500 text-sm">
                    © {{ date('Y') }} SACLI FOUNDIT. All rights reserved.
                </p>
            </div>
        </div>
    </footer>


</body>

</html>
