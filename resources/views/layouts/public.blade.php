<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' - ' : '' }}{{ config('app.name', 'SACLI FOUNDIT') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-sacli-green-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-sacli-green-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <a href="{{ route('home') }}" class="flex items-center">
                            <div class="flex-shrink-0">
                                <h1 class="text-2xl font-bold text-sacli-green-600">SACLI FOUNDIT</h1>
                            </div>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden sm:flex sm:items-center sm:space-x-6">
                        <a href="{{ route('home') }}" class="text-gray-700 hover:text-sacli-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150 {{ request()->routeIs('home') ? 'text-sacli-green-600 bg-sacli-green-50' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Search
                            </div>
                        </a>
                        <a href="{{ route('browse') }}" class="text-gray-700 hover:text-sacli-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150 {{ request()->routeIs('browse') ? 'text-sacli-green-600 bg-sacli-green-50' : '' }}">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                Browse
                            </div>
                        </a>
                        
                        @auth
                            <a href="{{ route('items.create') }}" class="text-gray-700 hover:text-sacli-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Report Item
                                </div>
                            </a>
                            <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-sacli-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    </svg>
                                    Dashboard
                                </div>
                            </a>
                            
                            <!-- User Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-gray-700 hover:text-sacli-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ Auth::user()->name }}
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                                    <a href="{{ route('items.my-items') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sacli-green-50 hover:text-sacli-green-600">My Items</a>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-sacli-green-50 hover:text-sacli-green-600">Profile</a>
                                    @if(Auth::user()->isAdmin())
                                        <div class="border-t border-gray-100"></div>
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-sacli-green-700 hover:bg-sacli-green-50 font-medium">Admin Panel</a>
                                    @endif
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-sacli-green-50 hover:text-sacli-green-600">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-sacli-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="bg-sacli-green-600 hover:bg-sacli-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Register
                            </a>
                        @endauth
                    </div>

                    <!-- Mobile menu button -->
                    <div class="sm:hidden flex items-center">
                        <button type="button" class="mobile-menu-button text-gray-700 hover:text-sacli-green-600 focus:outline-none focus:text-sacli-green-600 focus:ring-2 focus:ring-sacli-green-500 focus:ring-offset-2 rounded-md" aria-label="Toggle menu">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="mobile-menu hidden sm:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-sacli-green-200">
                    <a href="{{ route('home') }}" class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-sacli-green-600 hover:bg-sacli-green-50 {{ request()->routeIs('home') ? 'text-sacli-green-600 bg-sacli-green-50' : '' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search
                    </a>
                    <a href="{{ route('browse') }}" class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-sacli-green-600 hover:bg-sacli-green-50 {{ request()->routeIs('browse') ? 'text-sacli-green-600 bg-sacli-green-50' : '' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Browse
                    </a>
                    
                    @auth
                        <div class="border-t border-gray-200 my-2"></div>
                        <a href="{{ route('items.create') }}" class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-sacli-green-600 hover:bg-sacli-green-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Report Item
                        </a>
                        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-sacli-green-600 hover:bg-sacli-green-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('items.my-items') }}" class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-sacli-green-600 hover:bg-sacli-green-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            My Items
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-sacli-green-600 hover:bg-sacli-green-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profile
                        </a>
                        
                        @if(Auth::user()->isAdmin())
                            <div class="border-t border-gray-200 my-2"></div>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 rounded-md text-base font-medium text-sacli-green-700 hover:text-sacli-green-600 hover:bg-sacli-green-50 font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Admin Panel
                            </a>
                        @endif
                        
                        <div class="border-t border-gray-200 my-2"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-sacli-green-600 hover:bg-sacli-green-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    @else
                        <div class="border-t border-gray-200 my-2"></div>
                        <a href="{{ route('login') }}" class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-sacli-green-600 hover:bg-sacli-green-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="flex items-center px-3 py-2 rounded-md text-base font-medium bg-sacli-green-600 text-white hover:bg-sacli-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Register
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-sacli-green-200 mt-12">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <p class="text-gray-600 text-sm">
                        &copy; {{ date('Y') }} SACLI FOUNDIT. Helping reunite people with their belongings.
                    </p>
                </div>
            </div>
        </footer>

        <!-- Mobile menu toggle script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mobileMenuButton = document.querySelector('.mobile-menu-button');
                const mobileMenu = document.querySelector('.mobile-menu');
                
                if (mobileMenuButton && mobileMenu) {
                    mobileMenuButton.addEventListener('click', function() {
                        mobileMenu.classList.toggle('hidden');
                    });
                }
            });
        </script>
    </body>
</html>