@props(['current' => ''])

<div class="bg-white shadow-sm border-b border-sacli-green-200 mb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex space-x-2 overflow-x-auto">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
                class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-4 border-b-2 font-medium text-sm transition-all duration-200 {{ request()->routeIs('admin.dashboard')
                    ? 'border-sacli-green-600 text-sacli-green-600 bg-sacli-green-50 rounded-t-lg'
                    : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300 hover:bg-gray-50 rounded-t-lg' }}">
                <x-icon name="chart-bar" size="sm" />
                Dashboard
            </a>

            <!-- Pending Items -->
            <a href="{{ route('admin.pending-items') }}"
                class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-4 border-b-2 font-medium text-sm transition-all duration-200 {{ request()->routeIs('admin.pending-items')
                    ? 'border-sacli-green-600 text-sacli-green-600 bg-sacli-green-50 rounded-t-lg'
                    : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300 hover:bg-gray-50 rounded-t-lg' }}">
                <x-icon name="clock" size="sm" />
                Pending Items
                @if (isset($pendingCount) && $pendingCount > 0)
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>

            <!-- All Items -->
            <a href="{{ route('admin.items') }}"
                class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-4 border-b-2 font-medium text-sm transition-all duration-200 {{ request()->routeIs('admin.items*')
                    ? 'border-sacli-green-600 text-sacli-green-600 bg-sacli-green-50 rounded-t-lg'
                    : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300 hover:bg-gray-50 rounded-t-lg' }}">
                <x-icon name="archive-box" size="sm" />
                All Items
            </a>

            <!-- Categories -->
            <a href="{{ route('admin.categories') }}"
                class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-4 border-b-2 font-medium text-sm transition-all duration-200 {{ request()->routeIs('admin.categories')
                    ? 'border-sacli-green-600 text-sacli-green-600 bg-sacli-green-50 rounded-t-lg'
                    : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300 hover:bg-gray-50 rounded-t-lg' }}">
                <x-icon name="tag" size="sm" />
                Categories
            </a>

            <!-- Statistics -->
            <a href="{{ route('admin.statistics') }}"
                class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-4 border-b-2 font-medium text-sm transition-all duration-200 {{ request()->routeIs('admin.statistics*')
                    ? 'border-sacli-green-600 text-sacli-green-600 bg-sacli-green-50 rounded-t-lg'
                    : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300 hover:bg-gray-50 rounded-t-lg' }}">
                <x-icon name="chart-pie" size="sm" />
                Statistics
            </a>

            <!-- Chat Support -->
            <a href="{{ route('admin.chat.index') }}"
                class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-4 border-b-2 font-medium text-sm transition-all duration-200 {{ request()->routeIs('admin.chat*')
                    ? 'border-sacli-green-600 text-sacli-green-600 bg-sacli-green-50 rounded-t-lg'
                    : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300 hover:bg-gray-50 rounded-t-lg' }}">
                <x-icon name="chat-bubble-left-right" size="sm" />
                Chat Support
            </a>

            <!-- Notifications -->
            <a href="{{ route('admin.notifications.page') }}"
                class="whitespace-nowrap inline-flex items-center gap-2 px-4 py-4 border-b-2 font-medium text-sm transition-all duration-200 {{ request()->routeIs('admin.notifications*')
                    ? 'border-sacli-green-600 text-sacli-green-600 bg-sacli-green-50 rounded-t-lg'
                    : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300 hover:bg-gray-50 rounded-t-lg' }}">
                <x-icon name="bell" size="sm" />
                Notifications
            </a>
        </div>
    </div>
</div>
