<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-sacli-green-500 to-sacli-green-600 rounded-lg flex items-center justify-center shadow-md">
                    <x-icon name="chart-bar" size="md" class="text-white" />
                </div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Statistics & Analytics') }}
                </h2>
            </div>
            <div class="flex gap-3">
                <x-secondary-button onclick="window.print()" icon="printer" class="print:hidden">
                    Print Report
                </x-secondary-button>
                <x-secondary-button onclick="refreshStatistics()" icon="arrow-path" class="print:hidden">
                    Refresh
                </x-secondary-button>
                <div class="relative print:hidden">
                    <x-primary-button onclick="toggleExportMenu()" icon="arrow-down-tray">
                        Export
                    </x-primary-button>
                    <div id="exportMenu"
                        class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10 border border-gray-200">
                        <div class="py-1">
                            <a href="{{ route('admin.statistics.export', ['format' => 'json']) }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg transition-colors">
                                <x-icon name="document-text" size="sm" class="text-gray-400" />
                                Export as JSON
                            </a>
                            <a href="{{ route('admin.statistics.export', ['format' => 'csv']) }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-lg transition-colors">
                                <x-icon name="table-cells" size="sm" class="text-gray-400" />
                                Export as CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Print Header (hidden on screen, visible when printing) -->
            <div class="print-only hidden">
                <div class="print-header">
                    <div>
                        <h1>SACLI FOUNDIT - Statistics Report</h1>
                        <p class="text-sm text-gray-600">Comprehensive Analytics Dashboard</p>
                    </div>
                    <div class="timestamp">
                        <p>Generated: {{ now()->format('F d, Y') }}</p>
                        <p>Time: {{ now()->format('h:i A') }}</p>
                    </div>
                </div>
            </div>
            <!-- Overview Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 page-break-inside-avoid">
                <!-- Total Items -->
                <x-stat-card title="Total Items" :value="number_format($overviewStats['total_items'])" icon="archive-box" color="green">
                    <x-slot name="footer">
                        <p class="text-xs text-gray-500 mt-1">All time</p>
                    </x-slot>
                </x-stat-card>

                <!-- Verification Rate -->
                <x-stat-card title="Verification Rate" :value="number_format($successMetrics['verification_rate'], 1) . '%'" icon="check-circle" color="blue">
                    <x-slot name="footer">
                        <p class="text-xs text-gray-500 mt-1">Items approved</p>
                    </x-slot>
                </x-stat-card>

                <!-- Resolution Rate -->
                <x-stat-card title="Resolution Rate" :value="number_format($successMetrics['resolution_rate'], 1) . '%'" icon="sparkles" color="purple">
                    <x-slot name="footer">
                        <p class="text-xs text-gray-500 mt-1">Items resolved</p>
                    </x-slot>
                </x-stat-card>

                <!-- Pending Queue -->
                <x-stat-card title="Pending Queue" :value="number_format($performanceMetrics['pending_queue_size'])" icon="clock" color="orange">
                    <x-slot name="footer">
                        <p class="text-xs text-gray-500 mt-1">
                            {{ number_format($performanceMetrics['avg_pending_time_hours'], 1) }}h avg wait</p>
                    </x-slot>
                </x-stat-card>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8 page-break-inside-avoid">
                <!-- Submission Trends Chart -->
                <div
                    class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <x-icon name="chart-bar-square" size="md" class="text-sacli-green-600" />
                                <h3 class="text-lg font-medium text-gray-900">Submission Trends (Last 90 Days)</h3>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="updateTrendChart(30)"
                                    class="px-3 py-1 text-xs bg-sacli-green-100 text-sacli-green-700 rounded-lg hover:bg-sacli-green-200 transition-colors">30d</button>
                                <button onclick="updateTrendChart(60)"
                                    class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">60d</button>
                                <button onclick="updateTrendChart(90)"
                                    class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">90d</button>
                            </div>
                        </div>
                        <div class="h-64 rounded-lg">
                            <canvas id="submissionTrendsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Category Distribution Chart -->
                <div
                    class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <x-icon name="chart-pie" size="md" class="text-sacli-green-600" />
                            <h3 class="text-lg font-medium text-gray-900">Items by Category</h3>
                        </div>
                        <div class="h-64 rounded-lg">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics and Success Rates -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 page-break-before page-break-inside-avoid">
                <!-- Success Metrics -->
                <div
                    class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <x-icon name="trophy" size="md" class="text-sacli-green-600" />
                            <h3 class="text-lg font-medium text-gray-900">Success Metrics</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Overall Success Rate</span>
                                <span
                                    class="text-lg font-semibold text-sacli-green-600">{{ number_format($successMetrics['overall_success_rate'], 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-sacli-green-600 h-2 rounded-full transition-all duration-300"
                                    style="width: {{ $successMetrics['overall_success_rate'] }}%"></div>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 flex items-center gap-1">
                                    <x-icon name="clock" size="xs" class="text-gray-400" />
                                    Avg. Verification Time
                                </span>
                                <span
                                    class="text-lg font-semibold text-blue-600">{{ number_format($successMetrics['avg_verification_time_hours'], 1) }}h</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 flex items-center gap-1">
                                    <x-icon name="calendar" size="xs" class="text-gray-400" />
                                    Avg. Resolution Time
                                </span>
                                <span
                                    class="text-lg font-semibold text-purple-600">{{ number_format($successMetrics['avg_resolution_time_days'], 1) }}d</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div
                    class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <x-icon name="bolt" size="md" class="text-sacli-green-600" />
                            <h3 class="text-lg font-medium text-gray-900">Performance Metrics</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Verifications Today</span>
                                <span
                                    class="text-lg font-semibold text-sacli-green-600">{{ $performanceMetrics['verifications_today'] }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">This Week</span>
                                <span
                                    class="text-lg font-semibold text-blue-600">{{ $performanceMetrics['verifications_this_week'] }}</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 flex items-center gap-1">
                                    <x-icon name="users" size="xs" class="text-gray-400" />
                                    Active Users (30d)
                                </span>
                                <span
                                    class="text-lg font-semibold text-purple-600">{{ $performanceMetrics['active_users_30_days'] }}</span>
                            </div>

                            @if ($performanceMetrics['items_needing_attention'] > 0)
                                <div
                                    class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                    <span class="text-sm text-yellow-700 flex items-center gap-1">
                                        <x-icon name="exclamation-triangle" size="xs" class="text-yellow-600" />
                                        Items Needing Attention
                                    </span>
                                    <span
                                        class="text-lg font-semibold text-yellow-600">{{ $performanceMetrics['items_needing_attention'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Comparison Stats -->
                <div
                    class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <x-icon name="arrow-trending-up" size="md" class="text-sacli-green-600" />
                            <h3 class="text-lg font-medium text-gray-900">30-Day Comparison</h3>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Submissions</span>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-lg font-semibold">{{ $comparisonStats['current_period']['submissions'] }}</span>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full flex items-center gap-1 {{ $comparisonStats['percentage_changes']['submissions'] >= 0 ? 'bg-sacli-green-100 text-sacli-green-700' : 'bg-red-100 text-red-700' }}">
                                        <x-icon :name="$comparisonStats['percentage_changes']['submissions'] >= 0
                                            ? 'arrow-up'
                                            : 'arrow-down'" size="xs" />
                                        {{ abs($comparisonStats['percentage_changes']['submissions']) }}%
                                    </span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Verifications</span>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-lg font-semibold">{{ $comparisonStats['current_period']['verifications'] }}</span>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full flex items-center gap-1 {{ $comparisonStats['percentage_changes']['verifications'] >= 0 ? 'bg-sacli-green-100 text-sacli-green-700' : 'bg-red-100 text-red-700' }}">
                                        <x-icon :name="$comparisonStats['percentage_changes']['verifications'] >= 0
                                            ? 'arrow-up'
                                            : 'arrow-down'" size="xs" />
                                        {{ abs($comparisonStats['percentage_changes']['verifications']) }}%
                                    </span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Resolutions</span>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-lg font-semibold">{{ $comparisonStats['current_period']['resolutions'] }}</span>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full flex items-center gap-1 {{ $comparisonStats['percentage_changes']['resolutions'] >= 0 ? 'bg-sacli-green-100 text-sacli-green-700' : 'bg-red-100 text-red-700' }}">
                                        <x-icon :name="$comparisonStats['percentage_changes']['resolutions'] >= 0
                                            ? 'arrow-up'
                                            : 'arrow-down'" size="xs" />
                                        {{ abs($comparisonStats['percentage_changes']['resolutions']) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Statistics and Top Categories -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8 page-break-inside-avoid">
                <!-- Monthly Statistics Chart -->
                <div
                    class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <x-icon name="calendar-days" size="md" class="text-sacli-green-600" />
                            <h3 class="text-lg font-medium text-gray-900">Monthly Statistics ({{ date('Y') }})
                            </h3>
                        </div>
                        <div class="h-64 rounded-lg">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Top Categories -->
                <div
                    class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <x-icon name="star" size="md" class="text-sacli-green-600" />
                            <h3 class="text-lg font-medium text-gray-900">Top Categories</h3>
                        </div>
                        <div class="space-y-3">
                            @foreach ($topCategories as $index => $category)
                                <div
                                    class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 bg-gradient-to-br from-sacli-green-{{ 400 + $index * 100 }} to-sacli-green-{{ 500 + $index * 100 }} rounded-lg flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                                            {{ $index + 1 }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">{{ $category->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-sacli-green-600 h-2 rounded-full transition-all duration-300"
                                                style="width: {{ $topCategories->max('items_count') > 0 ? ($category->items_count / $topCategories->max('items_count')) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                        <span
                                            class="text-sm font-semibold text-gray-900 w-8 text-right">{{ $category->items_count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Statistics (if available) -->
            @if ($locationStats->count() > 0)
                <div
                    class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 hover:shadow-md transition-shadow duration-200 page-break-before page-break-inside-avoid">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <x-icon name="map-pin" size="md" class="text-sacli-green-600" />
                            <h3 class="text-lg font-medium text-gray-900">Top Locations</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($locationStats->take(6) as $location)
                                <div
                                    class="p-4 border border-gray-200 rounded-xl hover:shadow-md hover:border-sacli-green-300 transition-all duration-200">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="text-sm font-medium text-gray-900 truncate flex items-center gap-1">
                                            <x-icon name="map-pin" size="xs" class="text-gray-400" />
                                            {{ $location->location }}
                                        </h4>
                                        <span
                                            class="text-lg font-bold text-sacli-green-600">{{ $location->total_items }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 space-y-1">
                                        <div class="flex justify-between">
                                            <span>Lost:</span>
                                            <span class="font-medium">{{ $location->lost_items }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Found:</span>
                                            <span class="font-medium">{{ $location->found_items }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Resolved:</span>
                                            <span class="font-medium">{{ $location->resolved_items }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Print Footer (hidden on screen, visible when printing) -->
            <div class="print-only hidden print-footer">
                <p>SACLI FOUNDIT - Lost and Found Management System</p>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Print-specific styles for statistics dashboard */
            @media print {

                /* Enhanced page setup with proper margins */
                @page {
                    size: auto;
                    margin: 1.5cm 1.5cm 2.5cm 1.5cm;
                }

                @page :first {
                    margin-top: 1cm;
                }

                /* Optimize for Letter size (US) */
                @media (width: 8.5in) and (height: 11in) {
                    @page {
                        size: letter portrait;
                        margin: 0.75in 0.75in 1in 0.75in;
                    }
                }

                /* Optimize for A4 size (International) */
                @media (width: 210mm) and (height: 297mm) {
                    @page {
                        size: A4 portrait;
                        margin: 1.5cm 1.5cm 2cm 1.5cm;
                    }
                }

                /* Force exact color printing for important elements */
                .print-header,
                .print-footer,
                canvas {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }

                /* Enhanced print header with better layout */
                .print-header {
                    display: flex !important;
                    justify-content: space-between;
                    align-items: flex-start;
                    padding-bottom: 0.8cm;
                    margin-bottom: 0.8cm;
                    border-bottom: 3px solid #10B981;
                    page-break-after: avoid;
                }

                .print-header h1 {
                    font-size: 20pt;
                    font-weight: bold;
                    color: #047857;
                    margin: 0;
                    line-height: 1.2;
                }

                .print-header p {
                    margin: 0.15cm 0 0 0;
                    line-height: 1.3;
                }

                .print-header .timestamp {
                    text-align: right;
                    font-size: 9pt;
                    color: #555;
                    font-weight: 500;
                }

                /* Enhanced print footer with page numbers */
                .print-footer {
                    position: fixed;
                    bottom: 0.5cm;
                    left: 1.5cm;
                    right: 1.5cm;
                    text-align: center;
                    font-size: 8pt;
                    color: #666;
                    padding-top: 0.4cm;
                    border-top: 1px solid #999;
                    background: white;
                }

                .print-footer::after {
                    content: "Page " counter(page);
                    display: block;
                    margin-top: 0.15cm;
                    font-weight: 600;
                }

                /* Optimize charts for grayscale printing */
                canvas {
                    max-height: 280px !important;
                    page-break-inside: avoid !important;
                    margin: 0.4cm 0 !important;
                    filter: grayscale(0.3) contrast(1.1);
                }

                /* Chart containers */
                .h-64 {
                    height: auto !important;
                    min-height: 250px !important;
                }

                /* Ensure charts are visible and properly sized */
                #submissionTrendsChart,
                #categoryChart,
                #monthlyChart {
                    page-break-inside: avoid !important;
                }

                /* Statistics cards optimization */
                .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4 {
                    grid-template-columns: repeat(4, 1fr) !important;
                    gap: 0.3cm !important;
                    margin-bottom: 0.6cm !important;
                }

                /* Chart section grid */
                .grid.grid-cols-1.lg\\:grid-cols-2 {
                    grid-template-columns: repeat(2, 1fr) !important;
                    gap: 0.4cm !important;
                }

                /* Performance metrics grid */
                .grid.grid-cols-1.lg\\:grid-cols-3 {
                    grid-template-columns: repeat(3, 1fr) !important;
                    gap: 0.3cm !important;
                }

                /* Location stats grid */
                .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-3 {
                    grid-template-columns: repeat(3, 1fr) !important;
                    gap: 0.3cm !important;
                }

                /* Page break management for better layout */
                .page-break-before {
                    page-break-before: always !important;
                    break-before: page !important;
                    margin-top: 0 !important;
                    padding-top: 0.5cm !important;
                }

                /* Section spacing */
                .mb-8 {
                    margin-bottom: 0.7cm !important;
                }

                /* Card styling for print */
                .bg-white.overflow-hidden.shadow-sm {
                    box-shadow: none !important;
                    border: 1.5px solid #999 !important;
                    page-break-inside: avoid !important;
                }

                /* Progress bars - enhance visibility */
                .bg-gray-200 {
                    background-color: #e0e0e0 !important;
                    border: 1px solid #bbb !important;
                }

                .bg-sacli-green-600 {
                    background-color: #333 !important;
                }

                /* Stat card values - make them stand out */
                .text-3xl,
                .text-2xl {
                    font-weight: 700 !important;
                    color: #000 !important;
                }

                /* Section headers */
                h3.text-lg {
                    font-size: 11pt !important;
                    font-weight: 700 !important;
                    color: #000 !important;
                    margin-bottom: 0.3cm !important;
                }

                /* Optimize badges and labels */
                .text-xs {
                    font-size: 8pt !important;
                }

                .text-sm {
                    font-size: 9pt !important;
                }

                /* Top categories list optimization */
                .space-y-3>div {
                    margin-bottom: 0.2cm !important;
                    border: 1px solid #ddd !important;
                }

                /* Comparison stats arrows and badges */
                .rounded-full {
                    border: 1px solid #999 !important;
                }

                /* Warning/alert boxes */
                .bg-yellow-50 {
                    background-color: #f5f5f5 !important;
                    border: 1.5px solid #999 !important;
                }

                /* Ensure proper text contrast */
                .text-yellow-700,
                .text-yellow-600 {
                    color: #000 !important;
                    font-weight: 600 !important;
                }

                /* Icon optimization for print */
                iconify-icon,
                svg {
                    width: 10pt !important;
                    height: 10pt !important;
                    opacity: 0.7 !important;
                }

                /* Remove unnecessary decorative elements */
                .hover\\:shadow-md,
                .hover\\:bg-gray-50,
                .hover\\:bg-gray-100,
                .hover\\:border-sacli-green-300 {
                    box-shadow: none !important;
                    background-color: inherit !important;
                    border-color: inherit !important;
                }
            }
        </style>
    @endpush

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
                    btn.className =
                        'px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors';
                });
                event.target.className =
                    'px-3 py-1 text-xs bg-sacli-green-100 text-sacli-green-700 rounded-lg hover:bg-sacli-green-200 transition-colors';

                // Fetch new data
                fetch(`/admin/statistics/data?type=submission_trends&days=${days}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            submissionTrendsChart.data.labels = data.data.map(item => new Date(item.date)
                                .toLocaleDateString());
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

            // Print functionality with timestamp update and chart optimization
            window.addEventListener('beforeprint', function() {
                // Update timestamp before printing
                const timestampElement = document.querySelector('.print-header .timestamp');
                if (timestampElement) {
                    const now = new Date();
                    const dateOptions = {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };
                    const timeOptions = {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    };
                    timestampElement.innerHTML = `
                        <p>Generated: ${now.toLocaleDateString('en-US', dateOptions)}</p>
                        <p>Time: ${now.toLocaleTimeString('en-US', timeOptions)}</p>
                    `;
                }

                // Optimize charts for grayscale printing
                const grayscaleColors = ['#000000', '#333333', '#666666', '#999999', '#CCCCCC'];
                const patternColors = [
                    'rgba(0, 0, 0, 0.8)',
                    'rgba(51, 51, 51, 0.8)',
                    'rgba(102, 102, 102, 0.8)',
                    'rgba(153, 153, 153, 0.8)',
                    'rgba(204, 204, 204, 0.8)'
                ];

                // Store original colors for restoration
                if (submissionTrendsChart) {
                    submissionTrendsChart._originalColors = {
                        datasets: submissionTrendsChart.data.datasets.map(ds => ({
                            borderColor: ds.borderColor,
                            backgroundColor: ds.backgroundColor
                        }))
                    };

                    // Apply grayscale colors
                    submissionTrendsChart.data.datasets[0].borderColor = '#000000';
                    submissionTrendsChart.data.datasets[0].backgroundColor = 'rgba(0, 0, 0, 0.1)';
                    submissionTrendsChart.data.datasets[1].borderColor = '#333333';
                    submissionTrendsChart.data.datasets[1].backgroundColor = 'rgba(51, 51, 51, 0.1)';
                    submissionTrendsChart.data.datasets[2].borderColor = '#666666';
                    submissionTrendsChart.data.datasets[2].backgroundColor = 'rgba(102, 102, 102, 0.1)';

                    submissionTrendsChart.options.plugins.legend.labels.usePointStyle = true;
                    submissionTrendsChart.update();
                    submissionTrendsChart.resize();
                }

                if (categoryChart) {
                    categoryChart._originalColors = {
                        backgroundColor: [...categoryChart.data.datasets[0].backgroundColor]
                    };

                    // Apply grayscale pattern for better distinction
                    categoryChart.data.datasets[0].backgroundColor = grayscaleColors;
                    categoryChart.options.plugins.legend.labels.usePointStyle = true;
                    categoryChart.update();
                    categoryChart.resize();
                }

                if (monthlyChart) {
                    monthlyChart._originalColors = {
                        datasets: monthlyChart.data.datasets.map(ds => ({
                            backgroundColor: ds.backgroundColor
                        }))
                    };

                    // Apply grayscale colors
                    monthlyChart.data.datasets[0].backgroundColor = '#000000';
                    monthlyChart.data.datasets[1].backgroundColor = '#555555';
                    monthlyChart.data.datasets[2].backgroundColor = '#999999';

                    monthlyChart.options.plugins.legend.labels.usePointStyle = true;
                    monthlyChart.update();
                    monthlyChart.resize();
                }

                // Add print-specific styling to chart containers
                document.querySelectorAll('canvas').forEach(canvas => {
                    canvas.style.maxHeight = '280px';
                });
            });

            window.addEventListener('afterprint', function() {
                console.log('Print dialog closed - restoring original chart colors');

                // Restore original chart colors and sizes
                setTimeout(function() {
                    if (submissionTrendsChart && submissionTrendsChart._originalColors) {
                        submissionTrendsChart.data.datasets.forEach((ds, index) => {
                            ds.borderColor = submissionTrendsChart._originalColors.datasets[index]
                                .borderColor;
                            ds.backgroundColor = submissionTrendsChart._originalColors.datasets[index]
                                .backgroundColor;
                        });
                        submissionTrendsChart.options.plugins.legend.labels.usePointStyle = false;
                        submissionTrendsChart.update();
                        submissionTrendsChart.resize();
                        delete submissionTrendsChart._originalColors;
                    }

                    if (categoryChart && categoryChart._originalColors) {
                        categoryChart.data.datasets[0].backgroundColor = categoryChart._originalColors
                            .backgroundColor;
                        categoryChart.options.plugins.legend.labels.usePointStyle = false;
                        categoryChart.update();
                        categoryChart.resize();
                        delete categoryChart._originalColors;
                    }

                    if (monthlyChart && monthlyChart._originalColors) {
                        monthlyChart.data.datasets.forEach((ds, index) => {
                            ds.backgroundColor = monthlyChart._originalColors.datasets[index]
                                .backgroundColor;
                        });
                        monthlyChart.options.plugins.legend.labels.usePointStyle = false;
                        monthlyChart.update();
                        monthlyChart.resize();
                        delete monthlyChart._originalColors;
                    }

                    // Remove print-specific styling
                    document.querySelectorAll('canvas').forEach(canvas => {
                        canvas.style.maxHeight = '';
                    });
                }, 100);
            });

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
