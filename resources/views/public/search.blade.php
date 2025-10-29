<x-public-layout>
    <x-slot name="title">Search Results</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
        <!-- Search Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        @if (request('query'))
                            Search Results for "{{ request('query') }}"
                        @else
                            All Items
                        @endif
                    </h1>
                    <p class="text-gray-600 mt-1 flex items-center gap-2">
                        <x-icon name="document-text" size="sm" class="text-gray-400" />
                        {{ $items->total() ?? 0 }} items found
                    </p>
                </div>

                <!-- Search Form -->
                <div class="w-full lg:w-96">
                    <form action="{{ route('search') }}" method="GET" id="search-form">
                        <x-search-bar name="query" :value="request('query')" placeholder="Search items..." />
                    </form>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-4 sm:gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:w-64 flex-shrink-0">
                <!-- Mobile Filter Toggle -->
                <div class="lg:hidden mb-4">
                    <button onclick="toggleMobileFilters()"
                        class="w-full flex items-center justify-between bg-white border border-gray-200 rounded-lg px-4 py-3 text-left hover:bg-gray-50 transition duration-150 ease-in-out">
                        <span class="font-medium text-gray-900 flex items-center gap-2">
                            <x-icon name="funnel" size="sm" class="text-sacli-green-600" />
                            Filters
                        </span>
                        <x-icon name="chevron-down" size="sm" id="filter-chevron"
                            class="text-gray-400 transform transition-transform duration-200" />
                    </button>
                </div>

                <div id="filters-panel"
                    class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 sticky top-4 hidden lg:block">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Filters</h3>
                        <x-icon name="funnel" size="md" class="text-sacli-green-600" />
                    </div>

                    <form action="{{ route('search') }}" method="GET" id="filter-form">
                        <!-- Preserve search query -->
                        @if (request('query'))
                            <input type="hidden" name="query" value="{{ request('query') }}">
                        @endif

                        <!-- Item Type Filter -->
                        <div class="mb-4 sm:mb-6">
                            <h4
                                class="text-sm sm:text-base font-medium text-gray-900 mb-2 sm:mb-3 flex items-center gap-2">
                                <x-icon name="tag" size="sm" class="text-gray-400" />
                                Item Type
                            </h4>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="type" value=""
                                        class="text-sacli-green-600 focus:ring-sacli-green-500"
                                        {{ !request('type') ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">All Items</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="lost"
                                        class="text-sacli-green-600 focus:ring-sacli-green-500"
                                        {{ request('type') === 'lost' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 flex items-center gap-1.5">
                                        <x-icon name="exclamation-circle" size="xs" class="text-red-500" />
                                        Lost Items
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="found"
                                        class="text-sacli-green-600 focus:ring-sacli-green-500"
                                        {{ request('type') === 'found' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 flex items-center gap-1.5">
                                        <x-icon name="check-circle" size="xs" class="text-sacli-green-600" />
                                        Found Items
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-4 sm:mb-6">
                            <h4
                                class="text-sm sm:text-base font-medium text-gray-900 mb-2 sm:mb-3 flex items-center gap-2">
                                <x-icon name="squares-2x2" size="sm" class="text-gray-400" />
                                Category
                            </h4>
                            <select name="category_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sacli-green-500 focus:border-sacli-green-500 outline-none text-sm sm:text-base">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range Filter -->
                        <div class="mb-4 sm:mb-6">
                            <h4
                                class="text-sm sm:text-base font-medium text-gray-900 mb-2 sm:mb-3 flex items-center gap-2">
                                <x-icon name="calendar" size="sm" class="text-gray-400" />
                                Date Range
                            </h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">From</label>
                                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sacli-green-500 focus:border-sacli-green-500 outline-none text-sm sm:text-base">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">To</label>
                                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sacli-green-500 focus:border-sacli-green-500 outline-none text-sm sm:text-base">
                                </div>
                            </div>
                        </div>

                        <!-- Location Filter -->
                        <div class="mb-4 sm:mb-6">
                            <h4
                                class="text-sm sm:text-base font-medium text-gray-900 mb-2 sm:mb-3 flex items-center gap-2">
                                <x-icon name="map-pin" size="sm" class="text-gray-400" />
                                Location
                            </h4>
                            <input type="text" name="location" placeholder="Enter location..."
                                value="{{ request('location') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sacli-green-500 focus:border-sacli-green-500 outline-none text-sm sm:text-base">
                        </div>

                        <!-- Filter Actions -->
                        <div class="space-y-2">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-sacli-green-600 hover:bg-sacli-green-700 text-white py-2 px-4 rounded-lg font-medium transition-all duration-200 text-sm sm:text-base shadow-sm hover:shadow-md">
                                <x-icon name="check" size="sm" />
                                Apply Filters
                            </button>
                            <a href="{{ route('search') }}"
                                class="w-full inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg font-medium transition-all duration-200 text-center text-sm sm:text-base">
                                <x-icon name="x-mark" size="sm" />
                                Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Content -->
            <div class="flex-1">
                <!-- Sort Options -->
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 sm:mb-6 space-y-3 sm:space-y-0">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <span class="text-xs sm:text-sm font-medium text-gray-700 flex items-center gap-1.5">
                            <x-icon name="arrows-up-down" size="sm" class="text-gray-500" />
                            Sort by:
                        </span>
                        <div class="relative">
                            <select name="sort" onchange="updateSort(this.value)"
                                class="appearance-none bg-white pl-3 pr-10 py-2 border border-gray-300 rounded-lg shadow-sm hover:border-sacli-green-400 focus:ring-2 focus:ring-sacli-green-500 focus:border-sacli-green-500 outline-none text-xs sm:text-sm font-medium text-gray-700 cursor-pointer transition-all duration-200">
                                <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>
                                    🕐 Newest First</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>
                                    🕑 Oldest First</option>
                                <option value="relevance" {{ request('sort') === 'relevance' ? 'selected' : '' }}>
                                    ⭐ Most Relevant</option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- View Toggle -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        <span class="text-xs sm:text-sm font-medium text-gray-700">View:</span>
                        <div class="inline-flex rounded-lg border border-gray-300 bg-white shadow-sm">
                            <button onclick="toggleView('grid')" id="grid-view-btn"
                                class="p-2 rounded-l-lg bg-sacli-green-600 text-white hover:bg-sacli-green-700 transition-all duration-200"
                                title="Grid View">
                                <x-icon name="squares-2x2" size="sm" />
                            </button>
                            <button onclick="toggleView('list')" id="list-view-btn"
                                class="p-2 rounded-r-lg bg-white text-gray-600 hover:bg-gray-50 transition-all duration-200 border-l border-gray-300"
                                title="List View">
                                <x-icon name="bars-3" size="sm" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Grid/List -->
                @if (isset($items) && $items->count() > 0)
                    <div id="results-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        @foreach ($items as $item)
                            <a href="{{ route('items.show', $item->id) }}" class="block">
                                <x-item-card :item="$item" />
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if (method_exists($items, 'links'))
                        <div class="mt-8">
                            {{ $items->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <!-- No Results -->
                    <x-empty-state icon="magnifying-glass" title="No items found" :message="request('query')
                        ? 'No items match your search criteria. Try adjusting your filters or search terms.'
                        : 'No items have been reported yet. Be the first to report a lost or found item!'">
                        <div class="flex flex-col sm:flex-row gap-3 justify-center mt-6">
                            <a href="{{ route('search') }}"
                                class="inline-flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg font-medium transition-all duration-200">
                                <x-icon name="arrow-path" size="sm" />
                                Clear Search
                            </a>
                            @auth
                                <a href="{{ route('items.create') }}"
                                    class="inline-flex items-center justify-center gap-2 bg-sacli-green-600 hover:bg-sacli-green-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                    <x-icon name="plus-circle" size="sm" />
                                    Report an Item
                                </a>
                            @endauth
                        </div>
                    </x-empty-state>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Store view preference in local storage
        const VIEW_PREFERENCE_KEY = 'sacli-foundit-view-preference';

        function updateSort(value) {
            const url = new URL(window.location);
            url.searchParams.set('sort', value);
            window.location.href = url.toString();
        }

        function toggleView(view) {
            const container = document.getElementById('results-container');
            const gridBtn = document.getElementById('grid-view-btn');
            const listBtn = document.getElementById('list-view-btn');

            // Save preference
            localStorage.setItem(VIEW_PREFERENCE_KEY, view);

            if (view === 'grid') {
                container.className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6';
                gridBtn.className =
                    'p-2 rounded-l-lg bg-sacli-green-600 text-white hover:bg-sacli-green-700 transition-all duration-200';
                listBtn.className =
                    'p-2 rounded-r-lg bg-white text-gray-600 hover:bg-gray-50 transition-all duration-200 border-l border-gray-300';
            } else {
                container.className = 'flex flex-col space-y-4';
                gridBtn.className =
                    'p-2 rounded-l-lg bg-white text-gray-600 hover:bg-gray-50 transition-all duration-200';
                listBtn.className =
                    'p-2 rounded-r-lg bg-sacli-green-600 text-white hover:bg-sacli-green-700 transition-all duration-200 border-l border-gray-300';
            }
        }

        function toggleMobileFilters() {
            const panel = document.getElementById('filters-panel');
            const chevron = document.getElementById('filter-chevron');

            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                chevron.classList.add('rotate-180');
            } else {
                panel.classList.add('hidden');
                chevron.classList.remove('rotate-180');
            }
        }

        // Initialize view from saved preference or default to grid
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem(VIEW_PREFERENCE_KEY) || 'grid';
            toggleView(savedView);
        });
    </script>
</x-public-layout>
