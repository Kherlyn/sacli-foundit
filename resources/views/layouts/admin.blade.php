<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

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
        <!-- Admin Navigation -->
        <nav x-data="{ open: false }" class="bg-white border-b border-sacli-green-200 shadow-sm" role="navigation"
            aria-label="Admin navigation">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-sacli-green-500 to-sacli-green-600 rounded-lg flex items-center justify-center shadow-md">
                                    <x-icon name="shield-check" size="md" class="text-white" />
                                </div>
                                <span class="text-xl font-bold text-gray-900 hidden lg:block">ADMIN PANEL</span>
                            </a>
                        </div>
                    </div>

                    <!-- Right Side: Admin Info & Logout -->
                    <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                        <!-- Notification Bell for Admins -->
                        <div x-data="adminNotificationBell()" x-init="init()" class="relative">
                            <!-- Notification Bell Button -->
                            <button @click="toggleDropdown()" type="button"
                                class="relative p-2 text-gray-600 hover:text-sacli-green-600 hover:bg-gray-100 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-sacli-green-500">
                                <x-icon name="bell" size="md" />

                                <!-- Unread Badge -->
                                <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount"
                                    class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full min-w-[18px]">
                                </span>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="isOpen" @click.away="isOpen = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-lg border border-gray-200 z-50 max-h-[32rem] overflow-hidden"
                                style="display: none;">

                                <!-- Header -->
                                <div
                                    class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                                    <h3 class="text-sm font-semibold text-gray-900">Admin Notifications</h3>
                                    <button @click="markAllAsRead()" x-show="unreadCount > 0" type="button"
                                        class="text-xs text-sacli-green-600 hover:text-sacli-green-700 font-medium">
                                        Mark all as read
                                    </button>
                                </div>

                                <!-- Notifications List -->
                                <div class="overflow-y-auto max-h-96">
                                    <template x-if="notifications.length === 0">
                                        <div class="px-4 py-8 text-center">
                                            <x-icon name="bell-slash" size="lg"
                                                class="mx-auto text-gray-400 mb-2" />
                                            <p class="text-sm text-gray-500">No notifications yet</p>
                                        </div>
                                    </template>

                                    <template x-for="notification in notifications" :key="notification.id">
                                        <div @click="handleNotificationClick(notification)"
                                            :class="notification.read_at ? 'bg-white' : 'bg-blue-50'"
                                            class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors duration-150">
                                            <div class="flex items-start gap-3">
                                                <!-- Icon -->
                                                <div class="flex-shrink-0 mt-1">
                                                    <template x-if="notification.data.type === 'new_chat_message'">
                                                        <div
                                                            class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                            <x-icon name="chat-bubble-left-right" size="sm"
                                                                class="text-blue-600" />
                                                        </div>
                                                    </template>
                                                    <template x-if="notification.data.type === 'new_submission'">
                                                        <div
                                                            class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                            <x-icon name="document-plus" size="sm"
                                                                class="text-green-600" />
                                                        </div>
                                                    </template>
                                                    <template x-if="notification.data.type === 'system_event'">
                                                        <div
                                                            class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                                            <x-icon name="bell" size="sm"
                                                                class="text-purple-600" />
                                                        </div>
                                                    </template>
                                                </div>

                                                <!-- Content -->
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900"
                                                        x-text="notification.data.title || notification.data.message">
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1"
                                                        x-text="formatTime(notification.created_at)"></p>
                                                </div>

                                                <!-- Unread Indicator -->
                                                <div x-show="!notification.read_at" class="flex-shrink-0">
                                                    <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Footer -->
                                <div x-show="notifications.length > 0"
                                    class="px-4 py-2 border-t border-gray-200 bg-gray-50 text-center">
                                    <a href="{{ route('admin.notifications.page') }}"
                                        class="text-xs text-sacli-green-600 hover:text-sacli-green-700 font-medium">
                                        View all notifications
                                    </a>
                                </div>
                            </div>
                        </div>

                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-500 bg-white hover:text-sacli-green-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sacli-green-500 focus:ring-offset-2 transition-all duration-200 ease-in-out">
                                    <x-icon name="user-circle" size="sm" />
                                    <div>{{ auth()->guard('admin')->user()->name }}</div>
                                    <x-icon name="chevron-down" size="sm" />
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <!-- Back to Site -->
                                <x-dropdown-link :href="route('home')">
                                    <div class="flex items-center gap-2">
                                        <x-icon name="arrow-left" size="sm" />
                                        {{ __('Back to Site') }}
                                    </div>
                                </x-dropdown-link>

                                <!-- Logout -->
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('admin.logout')"
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
                    </div>

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button @click="open = ! open" :aria-expanded="open.toString()" aria-controls="mobile-menu"
                            aria-label="Toggle navigation menu"
                            class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-sacli-green-500 hover:bg-sacli-green-50 focus:outline-none focus:bg-sacli-green-50 focus:text-sacli-green-500 focus:ring-2 focus:ring-sacli-green-500 focus:ring-offset-2 transition-all duration-200 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div id="mobile-menu" :class="{ 'block': open, 'hidden': !open }"
                class="hidden sm:hidden transition-all duration-200 ease-in-out">
                <div class="pt-4 pb-1 border-t border-sacli-green-200">
                    <div class="px-4 flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-sacli-green-100 rounded-lg flex items-center justify-center">
                            <x-icon name="user-circle" size="md" class="text-sacli-green-600" />
                        </div>
                        <div>
                            <div class="font-medium text-base text-gray-800">
                                {{ auth()->guard('admin')->user()->name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">
                                {{ auth()->guard('admin')->user()->email }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('home')" icon="arrow-left">
                            {{ __('Back to Site') }}
                        </x-responsive-nav-link>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf

                            <x-responsive-nav-link :href="route('admin.logout')" icon="arrow-right-on-rectangle"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Admin Sub-Navigation -->
        <x-admin-navigation :pendingCount="$pendingCount ?? 0" />

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

    <!-- Additional Scripts -->
    @stack('scripts')

    <script>
        function adminNotificationBell() {
            return {
                isOpen: false,
                notifications: [],
                unreadCount: 0,
                pollInterval: null,

                init() {
                    this.fetchNotifications();
                    // Poll for new notifications every 30 seconds
                    this.pollInterval = setInterval(() => {
                        this.fetchNotifications();
                    }, 30000);
                },

                toggleDropdown() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen) {
                        this.fetchNotifications();
                    }
                },

                async fetchNotifications() {
                    try {
                        const response = await fetch('{{ route('admin.notifications.index') }}');
                        const data = await response.json();
                        this.notifications = data.notifications || [];
                        this.unreadCount = data.unread_count || 0;
                    } catch (error) {
                        console.error('Failed to fetch admin notifications:', error);
                    }
                },

                async handleNotificationClick(notification) {
                    await this.markAsRead(notification.id);

                    // Redirect to action URL if available
                    if (notification.data.action_url) {
                        window.location.href = notification.data.action_url;
                    }
                },

                async markAsRead(notificationId) {
                    try {
                        await fetch(`/admin/notifications/${notificationId}/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        // Update local state
                        const notification = this.notifications.find(n => n.id === notificationId);
                        if (notification && !notification.read_at) {
                            notification.read_at = new Date().toISOString();
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                        }
                    } catch (error) {
                        console.error('Failed to mark notification as read:', error);
                    }
                },

                async markAllAsRead() {
                    try {
                        await fetch('{{ route('admin.notifications.read-all') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        // Update local state
                        this.notifications.forEach(n => {
                            n.read_at = new Date().toISOString();
                        });
                        this.unreadCount = 0;
                    } catch (error) {
                        console.error('Failed to mark all as read:', error);
                    }
                },

                formatTime(timestamp) {
                    const date = new Date(timestamp);
                    const now = new Date();
                    const diff = Math.floor((now - date) / 1000); // seconds

                    if (diff < 60) return 'Just now';
                    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
                    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
                    if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`;

                    return date.toLocaleDateString();
                }
            }
        }
    </script>
</body>

</html>
