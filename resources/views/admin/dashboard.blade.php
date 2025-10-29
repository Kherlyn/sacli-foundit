<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-sacli-green-500 to-sacli-green-600 rounded-xl flex items-center justify-center shadow-md">
                    <x-icon name="chart-bar" size="md" class="text-white" />
                </div>
                <h2 class="font-semibold text-xl text-sacli-green-800 leading-tight">
                    {{ __('Admin Dashboard') }}
                </h2>
            </div>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
                <a href="{{ route('admin.pending-items') }}"
                    class="inline-flex items-center justify-center gap-2 bg-sacli-green-600 hover:bg-sacli-green-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                    <x-icon name="clock" size="sm" />
                    <span class="hidden sm:inline">Pending Items</span>
                    <span class="sm:hidden">Pending</span>
                    <span
                        class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-white text-sacli-green-600">
                        {{ $pendingCount }}
                    </span>
                </a>
                <a href="{{ route('admin.statistics') }}"
                    class="inline-flex items-center justify-center gap-2 bg-white border-2 border-sacli-green-600 hover:bg-sacli-green-50 text-sacli-green-600 px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200">
                    <x-icon name="chart-pie" size="sm" />
                    <span>Statistics</span>
                </a>
                <a href="{{ route('admin.categories') }}"
                    class="inline-flex items-center justify-center gap-2 bg-white border-2 border-sacli-green-600 hover:bg-sacli-green-50 text-sacli-green-600 px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200">
                    <x-icon name="tag" size="sm" />
                    <span class="hidden sm:inline">Manage Categories</span>
                    <span class="sm:hidden">Categories</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 lg:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Items -->
                <x-stat-card title="Total Items" :value="number_format($statistics['total_items'])" icon="archive-box" color="green">
                    <x-slot name="footer">
                        <span class="text-xs text-gray-500">All time</span>
                    </x-slot>
                </x-stat-card>

                <!-- Pending Items -->
                <x-stat-card title="Pending" :value="number_format($statistics['pending_items'])" icon="clock" color="orange">
                    <x-slot name="footer">
                        @if ($statistics['pending_items'] > 0)
                            <a href="{{ route('admin.pending-items') }}"
                                class="text-xs text-orange-600 hover:text-orange-800 font-medium hover:underline">
                                Needs review
                            </a>
                        @else
                            <span class="text-xs text-gray-500">All caught up!</span>
                        @endif
                    </x-slot>
                </x-stat-card>

                <!-- Verified Items -->
                <x-stat-card title="Verified" :value="number_format($statistics['verified_items'])" icon="check-circle" color="green">
                    <x-slot name="footer">
                        <span class="text-xs text-gray-500">Public items</span>
                    </x-slot>
                </x-stat-card>

                <!-- This Month -->
                <x-stat-card title="This Month" :value="number_format($statistics['items_this_month'])" icon="calendar" color="purple">
                    <x-slot name="footer">
                        <span class="text-xs text-gray-500">{{ now()->format('M Y') }}</span>
                    </x-slot>
                </x-stat-card>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-8">
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <x-icon name="bolt" size="md" class="text-sacli-green-600" />
                        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('admin.pending-items') }}"
                            class="flex items-center p-4 bg-sacli-green-50 hover:bg-sacli-green-100 rounded-xl border border-sacli-green-200 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-sacli-green-500 to-sacli-green-600 rounded-xl flex items-center justify-center shadow-md">
                                    <x-icon name="clipboard-document-check" size="md" class="text-white" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-semibold text-sacli-green-900">Review Pending Items</p>
                                <p class="text-xs text-sacli-green-600">{{ $pendingCount }} items waiting</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.categories') }}"
                            class="flex items-center p-4 bg-sacli-green-50 hover:bg-sacli-green-100 rounded-xl border border-sacli-green-200 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md">
                                    <x-icon name="tag" size="md" class="text-white" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-semibold text-sacli-green-900">Manage Categories</p>
                                <p class="text-xs text-sacli-green-600">Add or edit categories</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.statistics') }}"
                            class="flex items-center p-4 bg-sacli-green-50 hover:bg-sacli-green-100 rounded-xl border border-sacli-green-200 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                                    <x-icon name="chart-bar-square" size="md" class="text-white" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-semibold text-sacli-green-900">View Statistics</p>
                                <p class="text-xs text-sacli-green-600">Detailed analytics & charts</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Charts and Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Category Statistics Chart -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <x-icon name="chart-pie" size="md" class="text-sacli-green-600" />
                                <h3 class="text-lg font-semibold text-gray-900">Items by Category</h3>
                            </div>
                            <span class="text-sm text-gray-500">{{ $categoryStats->sum('count') }} total</span>
                        </div>
                        @if ($categoryStats->count() > 0)
                            <div class="space-y-4">
                                @foreach ($categoryStats as $stat)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                                            <div class="w-3 h-3 bg-sacli-green-500 rounded-full flex-shrink-0"></div>
                                            <span
                                                class="text-sm font-medium text-gray-700 truncate">{{ $stat->category_name }}</span>
                                        </div>
                                        <div class="flex items-center space-x-3 flex-shrink-0">
                                            <div class="w-32 bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-gradient-to-r from-sacli-green-500 to-sacli-green-600 h-2.5 rounded-full transition-all duration-500"
                                                    style="width: {{ $categoryStats->max('count') > 0 ? ($stat->count / $categoryStats->max('count')) * 100 : 0 }}%">
                                                </div>
                                            </div>
                                            <span
                                                class="text-sm font-semibold text-gray-900 w-8 text-right">{{ $stat->count }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <x-icon name="chart-pie" size="xl" class="text-gray-400" />
                                </div>
                                <p class="text-sm text-gray-500">No category data available</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Items -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <x-icon name="clock" size="md" class="text-sacli-green-600" />
                                <h3 class="text-lg font-semibold text-gray-900">Recent Items</h3>
                            </div>
                            <a href="{{ route('admin.items') }}"
                                class="inline-flex items-center gap-1 text-sm text-sacli-green-600 hover:text-sacli-green-800 font-medium transition-colors">
                                <span>View all</span>
                                <x-icon name="arrow-right" size="sm" />
                            </a>
                        </div>
                        @forelse($recentItems as $item)
                            <div
                                class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-xl transition-colors duration-200 border-b border-gray-100 last:border-b-0">
                                <div class="flex-shrink-0">
                                    @if ($item->images->count() > 0)
                                        <img src="{{ $item->images->first()->url }}" alt="{{ $item->title }}"
                                            class="w-12 h-12 object-cover rounded-lg shadow-sm">
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <x-icon name="photo" size="md" class="text-gray-400" />
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->title }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="text-xs text-gray-500 truncate">{{ $item->category->name }}</span>
                                        <span class="text-xs text-gray-400">•</span>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            @if ($item->type === 'lost') bg-red-100 text-red-700 @else bg-blue-100 text-blue-700 @endif">
                                            {{ ucfirst($item->type) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 flex flex-col items-end space-y-1">
                                    <x-status-badge :status="$item->status" />
                                    <span
                                        class="text-xs text-gray-400">{{ $item->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <x-icon name="inbox" size="xl" class="text-gray-400" />
                                </div>
                                <p class="text-sm text-gray-500">No recent items found</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Items Requiring Attention -->
            @if ($itemsRequiringAttention->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-6">
                            <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                                <x-icon name="exclamation-triangle" size="md" class="text-yellow-600" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Items Requiring Attention
                            </h3>
                            <span class="text-sm text-gray-500">(Pending > 7 days)</span>
                        </div>
                        <div class="space-y-3">
                            @foreach ($itemsRequiringAttention as $item)
                                <div
                                    class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-xl hover:bg-yellow-100 transition-colors duration-200">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">{{ $item->title }}</p>
                                        <div class="flex items-center gap-2 mt-1 text-xs text-gray-600">
                                            <span class="inline-flex items-center gap-1">
                                                <x-icon name="tag" size="xs" class="text-gray-400" />
                                                {{ $item->category->name }}
                                            </span>
                                            <span>•</span>
                                            <span class="inline-flex items-center gap-1">
                                                <x-icon name="user" size="xs" class="text-gray-400" />
                                                {{ $item->user->name }}
                                            </span>
                                            <span>•</span>
                                            <span class="inline-flex items-center gap-1">
                                                <x-icon name="clock" size="xs" class="text-gray-400" />
                                                {{ $item->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.pending-items') }}"
                                        class="inline-flex items-center gap-1 px-4 py-2 bg-sacli-green-600 hover:bg-sacli-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <x-icon name="eye" size="sm" />
                                        <span>Review</span>
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
                                        link.textContent = text.replace(/\(\d+\)/,
                                            `(${data.data.pending_items})`);
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
                    window.location.href = '{{ route('admin.pending-items') }}';
                }

                // Ctrl/Cmd + C to go to categories
                if ((e.ctrlKey || e.metaKey) && e.key === 'c') {
                    e.preventDefault();
                    window.location.href = '{{ route('admin.categories') }}';
                }
            });
        </script>
    @endpush
</x-app-layout>
