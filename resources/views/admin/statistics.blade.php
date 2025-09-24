<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Statistics & Analytics') }}
            </h2>
            <div class="flex space-x-4">
                <button onclick="refreshStatistics()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
                <div class="relative">
                    <button onclick="toggleExportMenu()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                    <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                        <div class="py-1">
                            <a href="{{ route('admin.statistics.export', ['format' => 'json']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export as JSON</a>
                            <a href="{{ route('admin.statistics.export', ['format' => 'csv']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export as CSV</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Overview Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Items -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-4 border-emerald-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Items</p>
                                <p class="text-3xl font-bold text-gray-900">{{ number_format($overviewStats['total_items']) }}</p>
                                <p class="text-xs text-emerald-600 font-medium mt-1">All time</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Verification Rate -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-4 border-blue-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Verification Rate</p>
                                <p class="text-3xl font-bold text-gray-900">{{ number_format($successMetrics['verification_rate'], 1) }}%</p>
                                <p class="text-xs text-blue-600 font-medium mt-1">Items approved</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resolution Rate -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-4 border-purple-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Resolution Rate</p>
                                <p class="text-3xl font-bold text-gray-900">{{ number_format($successMetrics['resolution_rate'], 1) }}%</p>
                                <p class="text-xs text-purple-600 font-medium mt-1">Items resolved</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Queue -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-4 border-yellow-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Pending Queue</p>
                                <p class="text-3xl font-bold text-gray-900">{{ number_format($performanceMetrics['pending_queue_size']) }}</p>
                                <p class="text-xs text-yellow-600 font-medium mt-1">{{ number_format($performanceMetrics['avg_pending_time_hours'], 1) }}h avg wait</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Submission Trends Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Submission Trends (Last 90 Days)</h3>
                            <div class="flex space-x-2">
                                <button onclick="updateTrendChart(30)" class="px-3 py-1 text-xs bg-emerald-100 text-emerald-700 rounded-md hover:bg-emerald-200">30d</button>
                                <button onclick="updateTrendChart(60)" class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">60d</button>
                                <button onclick="updateTrendChart(90)" class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">90d</button>
                            </div>
                        </div>
                        <div class="h-64">
                            <canvas id="submissionTrendsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Category Distribution Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Items by Category</h3>
                        <div class="h-64">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics and Success Rates -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Success Metrics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Success Metrics</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Overall Success Rate</span>
                                <span class="text-lg font-semibold text-emerald-600">{{ number_format($successMetrics['overall_success_rate'], 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $successMetrics['overall_success_rate'] }}%"></div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Avg. Verification Time</span>
                                <span class="text-lg font-semibold text-blue-600">{{ number_format($successMetrics['avg_verification_time_hours'], 1) }}h</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Avg. Resolution Time</span>
                                <span class="text-lg font-semibold text-purple-600">{{ number_format($successMetrics['avg_resolution_time_days'], 1) }}d</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Performance Metrics</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Verifications Today</span>
                                <span class="text-lg font-semibold text-emerald-600">{{ $performanceMetrics['verifications_today'] }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">This Week</span>
                                <span class="text-lg font-semibold text-blue-600">{{ $performanceMetrics['verifications_this_week'] }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Active Users (30d)</span>
                                <span class="text-lg font-semibold text-purple-600">{{ $performanceMetrics['active_users_30_days'] }}</span>
                            </div>
                            
                            @if($performanceMetrics['items_needing_attention'] > 0)
                            <div class="flex justify-between items-center p-2 bg-yellow-50 rounded-md">
                                <span class="text-sm text-yellow-700">Items Needing Attention</span>
                                <span class="text-lg font-semibold text-yellow-600">{{ $performanceMetrics['items_needing_attention'] }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Comparison Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">30-Day Comparison</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Submissions</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-semibold">{{ $comparisonStats['current_period']['submissions'] }}</span>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $comparisonStats['percentage_changes']['submissions'] >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $comparisonStats['percentage_changes']['submissions'] >= 0 ? '+' : '' }}{{ number_format($comparisonStats['percentage_changes']['submissions'], 1) }}%
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Verifications</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-semibold">{{ $comparisonStats['current_period']['verifications'] }}</span>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $comparisonStats['percentage_changes']['verifications'] >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $comparisonStats['percentage_changes']['verifications'] >= 0 ? '+' : '' }}{{ number_format($comparisonStats['percentage_changes']['verifications'], 1) }}%
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Resolutions</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-semibold">{{ $comparisonStats['current_period']['resolutions'] }}</span>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $comparisonStats['percentage_changes']['resolutions'] >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $comparisonStats['percentage_changes']['resolutions'] >= 0 ? '+' : '' }}{{ number_format($comparisonStats['percentage_changes']['resolutions'], 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Statistics and Top Categories -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Monthly Statistics Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Statistics ({{ date('Y') }})</h3>
                        <div class="h-64">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Top Categories -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Top Categories</h3>
                        <div class="space-y-3">
                            @foreach($topCategories as $index => $category)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-emerald-{{ 100 + ($index * 100) }} rounded-full flex items-center justify-center text-emerald-700 font-semibold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ $category->name }}</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $topCategories->max('items_count') > 0 ? ($category->items_count / $topCategories->max('items_count')) * 100 : 0 }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 w-8 text-right">{{ $category->items_count }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Statistics (if available) -->
            @if($locationStats->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Top Locations</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($locationStats->take(6) as $location)
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-sm font-medium text-gray-900 truncate">{{ $location->location }}</h4>
                                <span class="text-lg font-bold text-emerald-600">{{ $location->total_items }}</span>
                            </div>
                            <div class="text-xs text-gray-500 space-y-1">
                                <div class="flex justify-between">
                                    <span>Lost:</span>
                                    <span>{{ $location->lost_items }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Found:</span>
                                    <span>{{ $location->found_items }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Resolved:</span>
                                    <span>{{ $location->resolved_items }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart.js configuration with green theme
        Chart.defaults.color = '#374151';
        Chart.defaults.borderColor = '#E5E7EB';
        
        const greenColors = {
            primary: '#10B981',
            secondary: '#059669',
            light: '#D1FAE5',
            dark: '#047857'
        };

        // Initialize charts
        let submissionTrendsChart, categoryChart, monthlyChart;

        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
        });

        function initializeCharts() {
            // Submission Trends Chart
            const submissionCtx = document.getElementById('submissionTrendsChart').getContext('2d');
            const submissionData = @json($submissionTrends);
            
            submissionTrendsChart = new Chart(submissionCtx, {
                type: 'line',
                data: {
                    labels: submissionData.map(item => new Date(item.date).toLocaleDateString()),
                    datasets: [{
                        label: 'Total Submissions',
                        data: submissionData.map(item => item.total_submissions),
                        borderColor: greenColors.primary,
                        backgroundColor: greenColors.light,
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Lost Items',
                        data: submissionData.map(item => item.lost_submissions),
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Found Items',
                        data: submissionData.map(item => item.found_submissions),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Category Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const categoryData = @json($categoryStats);
            
            categoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(item => item.name),
                    datasets: [{
                        data: categoryData.map(item => item.items_count),
                        backgroundColor: [
                            greenColors.primary,
                            greenColors.secondary,
                            greenColors.dark,
                            '#34D399',
                            '#6EE7B7',
                            '#A7F3D0',
                            '#D1FAE5'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Monthly Chart
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyData = @json($monthlyStats);
            
            monthlyChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: monthlyData.map(item => `Month ${item.month}`),
                    datasets: [{
                        label: 'Total Items',
                        data: monthlyData.map(item => item.total_items),
                        backgroundColor: greenColors.primary
                    }, {
                        label: 'Verified Items',
                        data: monthlyData.map(item => item.verified_items),
                        backgroundColor: greenColors.secondary
                    }, {
                        label: 'Resolved Items',
                        data: monthlyData.map(item => item.resolved_items),
                        backgroundColor: greenColors.dark
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function updateTrendChart(days) {
            // Update active button
            document.querySelectorAll('button[onclick^="updateTrendChart"]').forEach(btn => {
                btn.className = 'px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200';
            });
            event.target.className = 'px-3 py-1 text-xs bg-emerald-100 text-emerald-700 rounded-md hover:bg-emerald-200';

            // Fetch new data
            fetch(`/admin/statistics/data?type=submission_trends&days=${days}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        submissionTrendsChart.data.labels = data.data.map(item => new Date(item.date).toLocaleDateString());
                        submissionTrendsChart.data.datasets[0].data = data.data.map(item => item.total_submissions);
                        submissionTrendsChart.data.datasets[1].data = data.data.map(item => item.lost_submissions);
                        submissionTrendsChart.data.datasets[2].data = data.data.map(item => item.found_submissions);
                        submissionTrendsChart.update();
                    }
                })
                .catch(error => console.error('Error updating chart:', error));
        }

        function refreshStatistics() {
            window.location.reload();
        }

        function toggleExportMenu() {
            const menu = document.getElementById('exportMenu');
            menu.classList.toggle('hidden');
        }

        // Close export menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('exportMenu');
            const button = event.target.closest('button[onclick="toggleExportMenu()"]');
            
            if (!button && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // Auto-refresh statistics every 5 minutes
        setInterval(function() {
            fetch('/admin/statistics/data?type=overview')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update overview stats without full page reload
                        console.log('Statistics refreshed automatically');
                    }
                })
                .catch(error => console.log('Auto-refresh failed:', error));
        }, 300000); // 5 minutes
    </script>
    @endpush
</x-app-layout>