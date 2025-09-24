<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <h2 class="font-semibold text-xl text-sacli-green-800 leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                <a href="{{ route('admin.pending-items') }}" class="bg-sacli-green-600 hover:bg-sacli-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out text-center">
                    <span class="hidden sm:inline">Pending Items</span>
                    <span class="sm:hidden">Pending</span>
                    ({{ $pendingCount }})
                </a>
                <a href="{{ route('admin.statistics') }}" class="bg-sacli-green-600 hover:bg-sacli-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out text-center">
                    Statistics
                </a>
                <a href="{{ route('admin.categories') }}" class="bg-sacli-green-600 hover:bg-sacli-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out text-center">
                    <span class="hidden sm:inline">Manage Categories</span>
                    <span class="sm:hidden">Categories</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 lg:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <!-- Total Items -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-4 border-sacli-green-500 hover:shadow-xl transition-shadow duration-200">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-sacli-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-sacli-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wide">Total Items</p>
                                <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ number_format($statistics['total_items']) }}</p>
                                <p class="text-xs text-sacli-green-600 font-medium mt-1">All time</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Items -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-4 border-yellow-500 hover:shadow-xl transition-shadow duration-200">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wide">Pending</p>
                                <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ number_format($statistics['pending_items']) }}</p>
                                <p class="text-xs text-yellow-600 font-medium mt-1">
                                    @if($statistics['pending_items'] > 0)
                                        <a href="{{ route('admin.pending-items') }}" class="hover:underline">Needs review</a>
                                    @else
                                        All caught up!
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Verified Items -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-4 border-sacli-green-500 hover:shadow-xl transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-sacli-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-sacli-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Verified</p>
                                <p class="text-3xl font-bold text-gray-900">{{ number_format($statistics['verified_items']) }}</p>
                                <p class="text-xs text-sacli-green-600 font-medium mt-1">Public items</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- This Month -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-4 border-sacli-green-600 hover:shadow-xl transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-sacli-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-sacli-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">This Month</p>
                                <p class="text-3xl font-bold text-gray-900">{{ number_format($statistics['items_this_month']) }}</p>
                                <p class="text-xs text-sacli-green-600 font-medium mt-1">{{ now()->format('M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 sm:mb-8">
                <div class="p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-medium text-gray-900 mb-3 sm:mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                        <a href="{{ route('admin.pending-items') }}" class="flex items-center p-3 sm:p-4 bg-sacli-green-50 hover:bg-sacli-green-100 rounded-lg border border-sacli-green-200 transition-colors duration-200">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-sacli-green-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <p class="text-xs sm:text-sm font-medium text-sacli-green-900">Review Pending Items</p>
                                <p class="text-xs text-sacli-green-600">{{ $pendingCount }} items waiting</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.categories') }}" class="flex items-center p-4 bg-sacli-green-50 hover:bg-sacli-green-100 rounded-lg border border-sacli-green-200 transition-colors duration-200">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-sacli-green-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-sacli-green-900">Manage Categories</p>
                                <p class="text-xs text-sacli-green-600">Add or edit categories</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.statistics') }}" class="flex items-center p-4 bg-sacli-green-50 hover:bg-sacli-green-100 rounded-lg border border-sacli-green-200 transition-colors duration-200">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-sacli-green-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-sacli-green-900">View Statistics</p>
                                <p class="text-xs text-sacli-green-600">Detailed analytics & charts</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Charts and Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <!-- Category Statistics Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-3 sm:mb-4 space-y-1 sm:space-y-0">
                            <h3 class="text-base sm:text-lg font-medium text-gray-900">Items by Category</h3>
                            <span class="text-xs sm:text-sm text-gray-500">{{ $categoryStats->sum('count') }} total items</span>
                        </div>
                        @if($categoryStats->count() > 0)
                            <div class="space-y-4">
                                @foreach($categoryStats as $stat)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2 sm:space-x-3 flex-1 min-w-0">
                                            <div class="w-2 h-2 sm:w-3 sm:h-3 bg-sacli-green-500 rounded-full flex-shrink-0"></div>
                                            <span class="text-xs sm:text-sm font-medium text-gray-700 truncate">{{ $stat->category_name }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2 sm:space-x-3 flex-shrink-0">
                                            <div class="w-16 sm:w-32 bg-gray-200 rounded-full h-1.5 sm:h-2">
                                                <div class="bg-sacli-green-600 h-1.5 sm:h-2 rounded-full transition-all duration-500" 
                                                     style="width: {{ $categoryStats->max('count') > 0 ? ($stat->count / $categoryStats->max('count')) * 100 : 0 }}%"></div>
                                            </div>
                                            <span class="text-xs sm:text-sm font-semibold text-gray-900 w-6 sm:w-8 text-right">{{ $stat->count }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No category data available</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Items -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center justify-between mb-3 sm:mb-4">
                            <h3 class="text-base sm:text-lg font-medium text-gray-900">Recent Items</h3>
                            <a href="{{ route('admin.items') }}" class="text-xs sm:text-sm text-sacli-green-600 hover:text-sacli-green-900 font-medium">View all</a>
                        </div>
                        @forelse($recentItems as $item)
                            <div class="flex items-center space-x-2 sm:space-x-4 p-2 sm:p-3 hover:bg-gray-50 rounded-lg transition-colors duration-200 border-b border-gray-100 last:border-b-0">
                                <div class="flex-shrink-0">
                                    @if($item->images->count() > 0)
                                        <img src="{{ Storage::url($item->images->first()->filename) }}" 
                                             alt="{{ $item->title }}" 
                                             class="w-8 h-8 sm:w-10 sm:h-10 object-cover rounded-lg">
                                    @else
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $item->title }}</p>
                                    <div class="flex items-center space-x-1 sm:space-x-2 mt-1">
                                        <span class="text-xs text-gray-500 truncate">{{ $item->category->name }}</span>
                                        <span class="text-xs text-gray-400 hidden sm:inline">•</span>
                                        <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded text-xs font-medium
                                            @if($item->type === 'lost') bg-red-100 text-red-700 @else bg-blue-100 text-blue-700 @endif">
                                            {{ ucfirst($item->type) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 flex flex-col sm:flex-row items-end sm:items-center space-y-1 sm:space-y-0 sm:space-x-2">
                                    <span class="inline-flex items-center px-1.5 sm:px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($item->status === 'verified') bg-sacli-green-100 text-sacli-green-800
                                        @elseif($item->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($item->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                    <span class="text-xs text-gray-400 hidden sm:block">{{ $item->created_at->diffForHumans() }}</span>
                                    <span class="text-xs text-gray-400 sm:hidden">{{ $item->created_at->format('M j') }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No recent items found</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Items Requiring Attention -->
            @if($itemsRequiringAttention->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            Items Requiring Attention (Pending > 7 days)
                        </h3>
                        <div class="space-y-2">
                            @foreach($itemsRequiringAttention as $item)
                                <div class="flex items-center justify-between p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $item->title }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $item->category->name }} • {{ $item->type }} • 
                                            Submitted {{ $item->created_at->diffForHumans() }} by {{ $item->user->name }}
                                        </p>
                                    </div>
                                    <a href="{{ route('admin.pending-items') }}" class="text-sacli-green-600 hover:text-sacli-green-900 text-sm font-medium">
                                        Review
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Add some interactive features to the dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Animate statistics cards on load
            const cards = document.querySelectorAll('.grid .bg-white');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Add hover effects to quick action buttons
            const quickActions = document.querySelectorAll('.bg-sacli-green-50');
            quickActions.forEach(action => {
                action.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.transition = 'transform 0.2s ease';
                });
                
                action.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Auto-refresh pending count every 30 seconds
            setInterval(function() {
                fetch('/admin/statistics/data?type=overview')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.pending_items !== undefined) {
                            // Update pending count in header
                            const pendingLinks = document.querySelectorAll('a[href*="pending-items"]');
                            pendingLinks.forEach(link => {
                                const text = link.textContent;
                                if (text.includes('(')) {
                                    link.textContent = text.replace(/\(\d+\)/, `(${data.data.pending_items})`);
                                }
                            });
                        }
                    })
                    .catch(error => console.log('Auto-refresh failed:', error));
            }, 30000);

            // Add click tracking for analytics
            document.querySelectorAll('a[href*="admin"]').forEach(link => {
                link.addEventListener('click', function() {
                    console.log('Admin navigation:', this.href);
                });
            });
        });

        // Function to refresh dashboard data
        function refreshDashboard() {
            window.location.reload();
        }

        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + R to refresh
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                refreshDashboard();
            }
            
            // Ctrl/Cmd + P to go to pending items
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.location.href = '{{ route("admin.pending-items") }}';
            }
            
            // Ctrl/Cmd + C to go to categories
            if ((e.ctrlKey || e.metaKey) && e.key === 'c') {
                e.preventDefault();
                window.location.href = '{{ route("admin.categories") }}';
            }
        });
    </script>
    @endpush
</x-app-layout>