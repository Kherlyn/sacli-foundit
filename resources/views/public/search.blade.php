<x-public-layout>
    <x-slot name="title">Search Results</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
        <!-- Search Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        @if(request('query'))
                            Search Results for "{{ request('query') }}"
                        @else
                            All Items
                        @endif
                    </h1>
                    <p class="text-gray-600 mt-1">
                        {{ $items->total() ?? 0 }} items found
                    </p>
                </div>

                <!-- Search Form -->
                <div class="w-full lg:w-96">
                    <form action="{{ route('search') }}" method="GET" id="search-form" class="flex flex-col gap-4">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input 
                                type="text" 
                                name="query" 
                                placeholder="Search items..." 
                                value="{{ request('query') }}"
                                class="flex-1 px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sacli-green-400 focus:border-sacli-green-400 outline-none text-sm sm:text-base"
                            >
                            <button 
                                type="submit" 
                                class="bg-sacli-green-400 hover:bg-sacli-green-500 text-white px-4 sm:px-6 py-2 rounded-lg font-medium transition duration-150 ease-in-out text-sm sm:text-base"
                            >
                                Search
                            </button>
                        </div>
                        
                        <div class="flex flex-wrap gap-4 text-sm">
                            <!-- Type Filter -->
                            <div class="flex items-center gap-2">
                                <select name="type" class="px-2 py-1 border border-gray-300 rounded-md text-sm">
                                    <option value="">All Types</option>
                                    <option value="lost" {{ request('type') === 'lost' ? 'selected' : '' }}>Lost Items</option>
                                    <option value="found" {{ request('type') === 'found' ? 'selected' : '' }}>Found Items</option>
                                </select>
                            </div>
                            
                            <!-- Category Filter -->
                            <div class="flex items-center gap-2">
                                <select name="category_id" class="px-2 py-1 border border-gray-300 rounded-md text-sm">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Date Range -->
                            <div class="flex items-center gap-2">
                                <input 
                                    type="date" 
                                    name="start_date" 
                                    value="{{ request('start_date') }}"
                                    placeholder="Start Date"
                                    class="px-2 py-1 border border-gray-300 rounded-md text-sm"
                                >
                                <span>to</span>
                                <input 
                                    type="date" 
                                    name="end_date" 
                                    value="{{ request('end_date') }}"
                                    placeholder="End Date"
                                    class="px-2 py-1 border border-gray-300 rounded-md text-sm"
                                >
                            </div>
                            
                            <!-- Location -->
                            <div class="flex items-center gap-2">
                                <input 
                                    type="text" 
                                    name="location" 
                                    value="{{ request('location') }}"
                                    placeholder="Location..."
                                    class="px-2 py-1 border border-gray-300 rounded-md text-sm"
                                >
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-4 sm:gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:w-64 flex-shrink-0">
                <!-- Mobile Filter Toggle -->
                <div class="lg:hidden mb-4">
                    <button 
                        onclick="toggleMobileFilters()" 
                        class="w-full flex items-center justify-between bg-white border border-gray-200 rounded-lg px-4 py-3 text-left hover:bg-gray-50 transition duration-150 ease-in-out"
                    >
                        <span class="font-medium text-gray-900">Filters</span>
                        <svg id="filter-chevron" class="w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
                
                <div id="filters-panel" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 sticky top-4 hidden lg:block">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Filters</h3>
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-sacli-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z" />
                        </svg>
                    </div>
                    
                    <form action="{{ route('search') }}" method="GET" id="filter-form">
                        <!-- Preserve search query -->
                        @if(request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif

                        <!-- Item Type Filter -->
                        <div class="mb-4 sm:mb-6">
                            <h4 class="text-sm sm:text-base font-medium text-gray-900 mb-2 sm:mb-3">Item Type</h4>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="" class="text-sacli-green-400 focus:ring-sacli-green-400" {{ !request('type') ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">All Items</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="lost" class="text-sacli-green-400 focus:ring-sacli-green-400" {{ request('type') === 'lost' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Lost Items</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="type" value="found" class="text-sacli-green-400 focus:ring-sacli-green-400" {{ request('type') === 'found' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Found Items</span>
                                </label>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-4 sm:mb-6">
                            <h4 class="text-sm sm:text-base font-medium text-gray-900 mb-2 sm:mb-3">Category</h4>
                            <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-sacli-green-400 focus:border-sacli-green-400 outline-none text-sm sm:text-base">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range Filter -->
                        <div class="mb-4 sm:mb-6">
                            <h4 class="text-sm sm:text-base font-medium text-gray-900 mb-2 sm:mb-3">Date Range</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">From</label>
                                    <input 
                                        type="date" 
                                        name="date_from" 
                                        value="{{ request('date_from') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-sacli-green-400 focus:border-sacli-green-400 outline-none text-sm sm:text-base"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">To</label>
                                    <input 
                                        type="date" 
                                        name="date_to" 
                                        value="{{ request('date_to') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-sacli-green-400 focus:border-sacli-green-400 outline-none text-sm sm:text-base"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Location Filter -->
                        <div class="mb-4 sm:mb-6">
                            <h4 class="text-sm sm:text-base font-medium text-gray-900 mb-2 sm:mb-3">Location</h4>
                            <input 
                                type="text" 
                                name="location" 
                                placeholder="Enter location..." 
                                value="{{ request('location') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-sacli-green-400 focus:border-sacli-green-400 outline-none text-sm sm:text-base"
                            >
                        </div>

                        <!-- Filter Actions -->
                        <div class="space-y-2">
                            <button 
                                type="submit" 
                                class="w-full bg-sacli-green-400 hover:bg-sacli-green-500 text-white py-2 px-4 rounded-md font-medium transition duration-150 ease-in-out text-sm sm:text-base"
                            >
                                Apply Filters
                            </button>
                            <a 
                                href="{{ route('search') }}" 
                                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-md font-medium transition duration-150 ease-in-out text-center block text-sm sm:text-base"
                            >
                                Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Content -->
            <div class="flex-1">
                <!-- Sort Options -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 sm:mb-6 space-y-3 sm:space-y-0">
                    <div class="flex items-center gap-2 sm:gap-4">
                        <span class="text-xs sm:text-sm text-gray-600">Sort by:</span>
                        <select 
                            name="sort" 
                            onchange="updateSort(this.value)"
                            class="px-2 sm:px-3 py-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-sacli-green-400 focus:border-sacli-green-400 outline-none text-xs sm:text-sm"
                        >
                            <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="relevance" {{ request('sort') === 'relevance' ? 'selected' : '' }}>Most Relevant</option>
                        </select>
                    </div>

                    <!-- View Toggle -->
                    <div class="flex items-center gap-1 sm:gap-2">
                        <button 
                            onclick="toggleView('grid')" 
                            id="grid-view-btn"
                            class="p-1.5 sm:p-2 rounded-md bg-sacli-green-400 text-white"
                        >
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button 
                            onclick="toggleView('list')" 
                            id="list-view-btn"
                            class="p-1.5 sm:p-2 rounded-md bg-gray-200 text-gray-600"
                        >
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Results Grid/List -->
                @if(isset($items) && $items->count() > 0)
                    <div id="results-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        @foreach($items as $item)
                            <div class="item-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition duration-150 ease-in-out group">
                                <div class="h-32 sm:h-48 bg-gray-200 flex items-center justify-center relative overflow-hidden">
                                    @if($item->images && $item->images->count() > 0)
                                        <img src="{{ asset('storage/' . $item->images->first()->filename) }}" 
                                             alt="{{ $item->title }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300 ease-in-out">
                                    @else
                                        <div class="text-center">
                                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-gray-400 text-sm">No Image</span>
                                        </div>
                                    @endif
                                    
                                    <!-- Status Badge -->
                                    <div class="absolute top-2 left-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->type === 'found' ? 'bg-sacli-green-100 text-sacli-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($item->type) }}
                                        </span>
                                    </div>

                                    <!-- Multiple Images Indicator -->
                                    @if($item->images && $item->images->count() > 1)
                                        <div class="absolute top-2 right-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-black bg-opacity-50 text-white">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $item->images->count() }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3 sm:p-4">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2 space-y-1 sm:space-y-0">
                                        <h3 class="text-sm sm:text-base font-semibold text-gray-900 truncate flex-1 sm:mr-2">{{ $item->title }}</h3>
                                        <span class="text-xs sm:text-sm text-gray-500 whitespace-nowrap">{{ $item->created_at->diffForHumans() }}</span>
                                    </div>
                                    
                                    <!-- Category -->
                                    <div class="flex items-center mb-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $item->category->name ?? 'Other' }}
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-600 text-xs sm:text-sm mb-2 sm:mb-3 line-clamp-2">{{ Str::limit($item->description, 100) }}</p>
                                    
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between space-y-2 sm:space-y-0">
                                        <div class="flex items-center text-xs sm:text-sm text-gray-500">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ Str::limit($item->location, 20) }}
                                        </div>
                                        <a href="{{ route('items.show', $item->id) }}" 
                                           class="text-sacli-green-400 hover:text-sacli-green-500 font-medium text-xs sm:text-sm flex items-center group">
                                            View Details
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 group-hover:translate-x-1 transition duration-150 ease-in-out" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if(method_exists($items, 'links'))
                        <div class="mt-8">
                            {{ $items->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <!-- No Results -->
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No items found</h3>
                        <p class="text-gray-600 mb-6">
                            @if(request('q'))
                                No items match your search criteria. Try adjusting your filters or search terms.
                            @else
                                No items have been reported yet. Be the first to report a lost or found item!
                            @endif
                        </p>
                        <div class="space-x-4">
                            <a href="{{ route('search') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition duration-150 ease-in-out">
                                Clear Search
                            </a>
                            @auth
                                <a href="#" class="bg-sacli-green-400 hover:bg-sacli-green-500 text-white px-6 py-2 rounded-lg font-medium transition duration-150 ease-in-out">
                                    Report an Item
                                </a>
                            @endauth
                        </div>
                    </div>
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
            const cards = document.querySelectorAll('.item-card');
            
            // Save preference
            localStorage.setItem(VIEW_PREFERENCE_KEY, view);
            
            if (view === 'grid') {
                container.className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6';
                cards.forEach(card => {
                    card.classList.remove('flex', 'flex-row');
                    card.querySelector('.h-32')?.classList.remove('w-48');
                });
                gridBtn.className = 'p-1.5 sm:p-2 rounded-md bg-sacli-green-400 text-white hover:bg-sacli-green-500 transition duration-150 ease-in-out';
                listBtn.className = 'p-1.5 sm:p-2 rounded-md bg-gray-200 text-gray-600 hover:bg-gray-300 transition duration-150 ease-in-out';
            } else {
                container.className = 'flex flex-col space-y-4';
                cards.forEach(card => {
                    card.classList.add('flex', 'flex-row');
                    const imgContainer = card.querySelector('.h-32');
                    if (imgContainer) {
                        imgContainer.classList.add('w-48');
                    }
                });
                gridBtn.className = 'p-1.5 sm:p-2 rounded-md bg-gray-200 text-gray-600 hover:bg-gray-300 transition duration-150 ease-in-out';
                listBtn.className = 'p-1.5 sm:p-2 rounded-md bg-sacli-green-400 text-white hover:bg-sacli-green-500 transition duration-150 ease-in-out';
            }
        }

        function toggleMobileFilters() {
            const panel = document.getElementById('filters-panel');
            const chevron = document.getElementById('filter-chevron');
            
            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                chevron.style.transform = 'rotate(180deg)';
            } else {
                panel.classList.add('hidden');
                chevron.style.transform = 'rotate(0deg)';
            }
        }

        // Initialize view from saved preference or default to grid
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem(VIEW_PREFERENCE_KEY) || 'grid';
            toggleView(savedView);
        });
    </script>
</x-public-layout>