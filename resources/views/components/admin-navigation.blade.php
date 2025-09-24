@props(['current' => ''])

<div class="bg-white shadow-sm border-b border-sacli-green-200 mb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex space-x-8 overflow-x-auto">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-150 {{ 
                   request()->routeIs('admin.dashboard') 
                   ? 'border-sacli-green-500 text-sacli-green-600' 
                   : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' 
               }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                    </svg>
                    Dashboard
                </div>
            </a>

            <!-- Pending Items -->
            <a href="{{ route('admin.pending-items') }}" 
               class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-150 {{ 
                   request()->routeIs('admin.pending-items') 
                   ? 'border-sacli-green-500 text-sacli-green-600' 
                   : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' 
               }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pending Items
                    @if(isset($pendingCount) && $pendingCount > 0)
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </div>
            </a>

            <!-- All Items -->
            <a href="{{ route('admin.items') }}" 
               class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-150 {{ 
                   request()->routeIs('admin.items*') 
                   ? 'border-sacli-green-500 text-sacli-green-600' 
                   : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' 
               }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    All Items
                </div>
            </a>

            <!-- Categories -->
            <a href="{{ route('admin.categories') }}" 
               class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-150 {{ 
                   request()->routeIs('admin.categories') 
                   ? 'border-sacli-green-500 text-sacli-green-600' 
                   : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' 
               }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Categories
                </div>
            </a>

            <!-- Statistics -->
            <a href="{{ route('admin.statistics') }}" 
               class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-150 {{ 
                   request()->routeIs('admin.statistics*') 
                   ? 'border-sacli-green-500 text-sacli-green-600' 
                   : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' 
               }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Statistics
                </div>
            </a>

            <!-- Notifications -->
            <a href="{{ route('admin.notifications') }}" 
               class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-150 {{ 
                   request()->routeIs('admin.notifications*') 
                   ? 'border-sacli-green-500 text-sacli-green-600' 
                   : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' 
               }}">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 19H6a2 2 0 01-2-2V7a2 2 0 012-2h5m5 0v5a2 2 0 002 2h5M9 7h1m-1 4h1m-1 4h1"></path>
                    </svg>
                    Notifications
                </div>
            </a>
        </div>
    </div>
</div>