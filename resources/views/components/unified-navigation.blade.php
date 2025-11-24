<x-skip-link />

<nav x-data="{ open: false }" class="bg-white border-b border-sacli-green-200 shadow-sm" role="navigation"
    aria-label="Main navigation">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center gap-2">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-sacli-green-500 to-sacli-green-600 rounded-lg flex items-center justify-center shadow-md">
                            <x-icon name="magnifying-glass" size="md" class="text-sacli-yellow-500" />
                        </div>
                        <span class="text-xl font-bold text-gray-900 hidden lg:block">SACLI FOUNDIT</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs(
                            'dashboard',
                            'items.my-items',
                            'items.show',
                            'items.edit',
                            'items.view',
                        )" icon="squares-2x2">
                            {{ __('My Items') }}
                        </x-nav-link>
                    @endauth
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home', 'search')" icon="magnifying-glass">
                        {{ __('Search') }}
                    </x-nav-link>
                    <x-nav-link :href="route('browse')" :active="request()->routeIs('browse')" icon="squares-2x2">
                        {{ __('Browse') }}
                    </x-nav-link>
                    <x-nav-link :href="route('about')" :active="request()->routeIs('about')" icon="information-circle">
                        {{ __('About') }}
                    </x-nav-link>

                    @if (auth()->guard('admin')->check())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')" icon="shield-check"
                            class="text-sacli-green-700 font-semibold">
                            {{ __('Admin') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Right Side: Submit Button & Settings -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                @auth
                    <!-- Submit Button -->
                    <a href="{{ route('items.create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-sacli-green-600 hover:bg-sacli-green-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
                        <x-icon name="plus-circle" size="sm" />
                        {{ __('Submit') }}
                    </a>

                    <!-- Notification Bell -->
                    <x-notification-bell />

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-500 bg-white hover:text-sacli-green-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sacli-green-500 focus:ring-offset-2 transition-all duration-200 ease-in-out">
                                <x-icon name="user-circle" size="sm" />
                                <div>{{ Auth::user()->name }}</div>
                                <x-icon name="chevron-down" size="sm" />
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                <div class="flex items-center gap-2">
                                    <x-icon name="user" size="sm" />
                                    {{ __('Profile') }}
                                </div>
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    <div class="flex items-center gap-2">
                                        <x-icon name="arrow-right-on-rectangle" size="sm" />
                                        {{ __('Log Out') }}
                                    </div>
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <!-- Guest Links -->
                    <a href="{{ route('login') }}"
                        class="text-gray-700 hover:text-sacli-green-600 font-medium text-sm transition duration-150 ease-in-out">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-sacli-green-600 hover:bg-sacli-green-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
                        Register
                    </a>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" :aria-expanded="open.toString()" aria-controls="mobile-menu"
                    aria-label="Toggle navigation menu"
                    class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-sacli-green-500 hover:bg-sacli-green-50 focus:outline-none focus:bg-sacli-green-50 focus:text-sacli-green-500 focus:ring-2 focus:ring-sacli-green-500 focus:ring-offset-2 transition-all duration-200 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div id="mobile-menu" :class="{ 'block': open, 'hidden': !open }"
        class="hidden sm:hidden transition-all duration-200 ease-in-out">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard', 'items.my-items', 'items.show', 'items.edit', 'items.view')" icon="squares-2x2">
                    {{ __('My Items') }}
                </x-responsive-nav-link>
            @endauth
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home', 'search')" icon="magnifying-glass">
                {{ __('Search') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('browse')" :active="request()->routeIs('browse')" icon="squares-2x2">
                {{ __('Browse') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('about')" :active="request()->routeIs('about')" icon="information-circle">
                {{ __('About') }}
            </x-responsive-nav-link>

            @auth
                <div class="border-t border-sacli-green-200 pt-2 mt-2">
                    @if (auth()->guard('admin')->check())
                        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')" icon="shield-check"
                            class="text-sacli-green-700 font-semibold">
                            {{ __('Admin Panel') }}
                        </x-responsive-nav-link>
                    @endif

                    <!-- Submit Button for Mobile -->
                    <div class="px-3 py-2">
                        <a href="{{ route('items.create') }}"
                            class="block w-full text-center px-4 py-3 bg-sacli-green-600 hover:bg-sacli-green-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200 ease-in-out">
                            <span class="inline-flex items-center gap-2">
                                <x-icon name="plus-circle" size="sm" />
                                {{ __('Submit Item') }}
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-sacli-green-200">
                    <div class="px-4 flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-sacli-green-100 rounded-lg flex items-center justify-center">
                            <x-icon name="user-circle" size="md" class="text-sacli-green-600" />
                        </div>
                        <div>
                            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('profile.edit')" icon="user">
                            {{ __('Profile') }}
                        </x-responsive-nav-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-responsive-nav-link :href="route('logout')" icon="arrow-right-on-rectangle"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            @else
                <!-- Guest Mobile Menu -->
                <div class="border-t border-sacli-green-200 pt-2 mt-2">
                    <x-responsive-nav-link :href="route('login')" icon="arrow-right-on-rectangle">
                        {{ __('Login') }}
                    </x-responsive-nav-link>

                    <div class="px-3 py-2">
                        <a href="{{ route('register') }}"
                            class="block w-full text-center px-4 py-3 bg-sacli-green-600 hover:bg-sacli-green-700 text-white font-medium rounded-lg shadow-sm transition-all duration-200 ease-in-out">
                            Register
                        </a>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</nav>
