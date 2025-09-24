<x-public-layout>
    <x-slot name="title">Browse Items</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Browse Items</h1>
            <p class="text-gray-600">
                @if($selectedCategory)
                    Showing items in {{ $selectedCategory->name }} category
                @else
                    Browse all lost and found items by category
                @endif
            </p>
        </div>

        <!-- Category Filter Tabs -->
        <div class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 overflow-x-auto">
                    <a href="{{ route('browse') }}" 
                       class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out {{ !request('category') ? 'border-sacli-green-400 text-sacli-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        All Categories
                    </a>
                    @php
                        $categoryIcons = [
                            'Electronics' => '📱',
                            'Clothing' => '👕',
                            'Documents' => '📄',
                            'Keys' => '🔑',
                            'Jewelry' => '💍',
                            'Books & Media' => '📚',
                            'Sports Equipment' => '⚽',
                            'Personal Items' => '👜',
                            'Pets' => '🐕',
                            'Other' => '📦',
                        ];
                    @endphp
                    @foreach($categories as $category)
                        <a href="{{ route('browse', ['category' => $category->id]) }}" 
                           class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition duration-150 ease-in-out {{ request('category') == $category->id ? 'border-sacli-green-400 text-sacli-green-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <span>{{ $categoryIcons[$category->name] ?? '📦' }}</span>
                            {{ $category->name }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
                    
                    <form action="{{ route('browse') }}" method="GET" id="filter-form">
                        <!-- Preserve category -->
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif

                        <!-- Item Type Filter -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Item Type</h4>
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

                        <!-- Date Range Filter -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Date Range</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">From</label>
                                    <input 
                                        type="date" 
                                        name="date_from" 
                                        value="{{ request('date_from') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-sacli-green-400 focus:border-sacli-green-400 outline-none text-sm"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">To</label>
                                    <input 
                                        type="date" 
                                        name="date_to" 
                                        value="{{ request('date_to') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-sacli-green-400 focus:border-sacli-green-400 outline-none text-sm"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Location Filter -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Location</h4>
                            <input 
                                type="text" 
                                name="location" 
                                placeholder="Enter location..." 
                                value="{{ request('location') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-sacli-green-400 focus:border-sacli-green-400 outline-none text-sm"
                            >
                        </div>

                        <!-- Status Filter -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Status</h4>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="status[]" value="verified" class="text-sacli-green-400 focus:ring-sacli-green-400" {{ in_array('verified', request('status', [])) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Verified</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="status[]" value="resolved" class="text-sacli-green-400 focus:ring-sacli-green-400" {{ in_array('resolved', request('status', [])) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Resolved</span>
                                </label>
                            </div>
                        </div>

                        <!-- Filter Actions -->
                        <div class="space-y-2">
                            <button 
                                type="submit" 
                                class="w-full bg-sacli-green-400 hover:bg-sacli-green-500 text-white py-2 px-4 rounded-md font-medium transition duration-150 ease-in-out"
                            >
                                Apply Filters
                            </button>
                            <a 
                                href="{{ route('browse', request('category') ? ['category' => request('category')] : []) }}" 
                                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-md font-medium transition duration-150 ease-in-out text-center block"
                            >
                                Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Items Content -->
            <div class="flex-1">
                <!-- Sort and View Options -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">
                            {{ isset($items) ? $items->total() : 0 }} items found
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">Sort by:</span>
                            <select 
                                name="sort" 
                                onchange="updateSort(this.value)"
                                class="px-3 py-1 border border-gray-300 rounded-md focus:ring-2 focus:ring-sacli-green-400 focus:border-sacli-green-400 outline-none text-sm"
                            >
                                <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="category" {{ request('sort') === 'category' ? 'selected' : '' }}>By Category</option>
                                <option value="location" {{ request('sort') === 'location' ? 'selected' : '' }}>By Location</option>
                            </select>
                        </div>
                    </div>

                    <!-- View Toggle -->
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600">View:</span>
                        <button 
                            onclick="toggleView('grid')" 
                            id="grid-view-btn"
                            class="p-2 rounded-md bg-sacli-green-400 text-white"
                            title="Grid View"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button 
                            onclick="toggleView('list')" 
                            id="list-view-btn"
                            class="p-2 rounded-md bg-gray-200 text-gray-600"
                            title="List View"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Items Grid/List -->
                @if(isset($items) && $items->count() > 0)
                    <div id="results-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($items as $item)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition duration-150 ease-in-out group">
                                <!-- Item Image -->
                                <div class="h-48 bg-gray-200 flex items-center justify-center relative overflow-hidden">
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

                                    <!-- Category Badge -->
                                    <div class="absolute top-2 right-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white bg-opacity-90 text-gray-700">
                                            {{ $categoryIcons[$item->category->name ?? 'Other'] ?? '📦' }}
                                            {{ $item->category->name ?? 'Other' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Item Details -->
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-semibold text-gray-900 truncate">{{ $item->title }}</h3>
                                        <span class="text-sm text-gray-500 ml-2">{{ $item->created_at->diffForHumans() }}</span>
                                    </div>
                                    
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($item->description, 100) }}</p>
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ Str::limit($item->location, 20) }}
                                        </div>
                                        <a href="{{ route('items.show', $item->id) }}" 
                                           class="text-sacli-green-400 hover:text-sacli-green-500 font-medium text-sm flex items-center group">
                                            View Details
                                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition duration-150 ease-in-out" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} results
                                </div>
                                {{ $items->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                @else
                    <!-- No Results -->
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No items found</h3>
                        <p class="text-gray-600 mb-6">
                            @if(request()->hasAny(['category', 'type', 'location', 'date_from', 'date_to', 'status']))
                                No items match your current filters. Try adjusting your search criteria.
                            @else
                                No items have been reported in this category yet. Be the first to report an item!
                            @endif
                        </p>
                        <div class="space-x-4">
                            <a href="{{ route('browse') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-medium transition duration-150 ease-in-out">
                                View All Items
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
        function updateSort(value) {
            const url = new URL(window.location);
            url.searchParams.set('sort', value);
            window.location.href = url.toString();
        }

        function toggleView(view) {
            const container = document.getElementById('results-container');
            const gridBtn = document.getElementById('grid-view-btn');
            const listBtn = document.getElementById('list-view-btn');
            
            if (view === 'grid') {
                container.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
                gridBtn.className = 'p-2 rounded-md bg-sacli-green-400 text-white';
                listBtn.className = 'p-2 rounded-md bg-gray-200 text-gray-600';
            } else {
                container.className = 'space-y-4';
                // Update each item to list view
                const items = container.children;
                for (let item of items) {
                    item.className = 'bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition duration-150 ease-in-out flex';
                }
                gridBtn.className = 'p-2 rounded-md bg-gray-200 text-gray-600';
                listBtn.className = 'p-2 rounded-md bg-sacli-green-400 text-white';
            }
        }
    </script>
</x-public-layout>