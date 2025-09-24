<x-public-layout>
    <x-slot name="title">Home</x-slot>

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-sacli-green-50 via-white to-sacli-green-50 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="#10B981" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="text-center">
                <div class="mb-8">
                    <div class="inline-flex items-center px-4 py-2 bg-sacli-green-100 text-sacli-green-800 rounded-full text-sm font-medium mb-4">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Trusted by SACLI Community
                    </div>
                </div>
                
                <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                    Lost Something? 
                    <span class="text-sacli-green-400 block lg:inline">Found Something?</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto leading-relaxed">
                    SACLI FOUNDIT helps reunite people with their belongings through our comprehensive lost and found platform. 
                    Search verified items or report what you've lost or found.
                </p>

                <!-- Search Form -->
                <div class="max-w-2xl mx-auto mb-8">
                    <form action="{{ route('search') }}" method="GET" class="relative">
                        <div class="flex flex-col sm:flex-row gap-2 p-2 bg-white rounded-xl shadow-lg border border-gray-200">
                            <div class="flex-1 relative">
                                <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input 
                                    type="text" 
                                    name="query" 
                                    placeholder="Search for lost or found items..." 
                                    value="{{ request('query') }}"
                                    class="w-full pl-12 pr-4 py-3 text-lg border-0 rounded-lg focus:ring-2 focus:ring-sacli-green-400 focus:outline-none"
                                >
                            </div>
                            <button 
                                type="submit" 
                                class="bg-sacli-green-400 hover:bg-sacli-green-500 text-white px-8 py-3 rounded-lg font-semibold text-lg transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-sacli-green-400 focus:ring-offset-2 shadow-sm"
                            >
                                Search
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Quick Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ route('items.create', ['type' => 'lost']) }}" class="inline-flex items-center bg-white hover:bg-gray-50 text-sacli-green-400 border-2 border-sacli-green-400 px-6 py-3 rounded-lg font-semibold transition duration-150 ease-in-out shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Report Lost Item
                        </a>
                        <a href="{{ route('items.create', ['type' => 'found']) }}" class="inline-flex items-center bg-sacli-green-400 hover:bg-sacli-green-500 text-white border-2 border-sacli-green-400 px-6 py-3 rounded-lg font-semibold transition duration-150 ease-in-out shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Report Found Item
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center bg-sacli-green-400 hover:bg-sacli-green-500 text-white px-8 py-3 rounded-lg font-semibold transition duration-150 ease-in-out shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-3-3h3m-3 0h3m-3-3h3m-3 0h3" />
                            </svg>
                            Register to Report Items
                        </a>
                        <a href="{{ route('browse') }}" class="inline-flex items-center bg-white hover:bg-gray-50 text-sacli-green-400 border-2 border-sacli-green-400 px-6 py-3 rounded-lg font-semibold transition duration-150 ease-in-out shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Browse Items
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Browse by Category</h2>
            <p class="text-lg text-gray-600">Find items by category to narrow down your search</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
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

            @foreach($categories->take(6) as $category)
                <a href="{{ route('browse', ['category' => $category->id]) }}" class="group">
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md hover:border-sacli-green-400 transition duration-150 ease-in-out text-center">
                        <div class="text-4xl mb-3">{{ $categoryIcons[$category->name] ?? '📦' }}</div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-sacli-green-400 transition duration-150 ease-in-out">
                            {{ $category->name }}
                        </h3>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Recent Items Section -->
    <div class="bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Recently Reported Items</h2>
                <p class="text-lg text-gray-600">Latest verified items in our database</p>
            </div>

            <!-- Recent items grid -->
            @if($recentItems && $recentItems->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($recentItems as $item)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition duration-150 ease-in-out">
                            <div class="h-48 bg-gray-200 flex items-center justify-center relative overflow-hidden">
                                @if($item->images && $item->images->count() > 0)
                                    <img src="{{ asset('storage/' . $item->images->first()->filename) }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="text-center">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-gray-400 text-sm">No Image</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->type === 'found' ? 'bg-sacli-green-100 text-sacli-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($item->type) }}
                                    </span>
                                    <span class="text-sm text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                                </div>
                                <h3 class="font-semibold text-gray-900 mb-2">{{ $item->title }}</h3>
                                <p class="text-gray-600 text-sm mb-3">{{ Str::limit($item->description, 100) }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">📍 {{ Str::limit($item->location, 20) }}</span>
                                    <a href="{{ route('items.show', $item->id) }}" class="text-sacli-green-400 hover:text-sacli-green-500 font-medium text-sm">
                                        View Details →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No items yet</h3>
                    <p class="text-gray-600 mb-6">Be the first to report a lost or found item!</p>
                    @auth
                        <a href="{{ route('items.create') }}" class="bg-sacli-green-400 hover:bg-sacli-green-500 text-white px-6 py-2 rounded-lg font-medium transition duration-150 ease-in-out">
                            Report an Item
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="bg-sacli-green-400 hover:bg-sacli-green-500 text-white px-6 py-2 rounded-lg font-medium transition duration-150 ease-in-out">
                            Register to Report Items
                        </a>
                    @endif
                </div>
            @endif

            <div class="text-center mt-12">
                <a href="{{ route('browse') }}" class="bg-sacli-green-400 hover:bg-sacli-green-500 text-white px-8 py-3 rounded-lg font-semibold transition duration-150 ease-in-out">
                    View All Items
                </a>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">How It Works</h2>
            <p class="text-lg text-gray-600">Simple steps to help reunite people with their belongings</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-sacli-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">🔍</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Search</h3>
                <p class="text-gray-600">Search our database of found items or browse by category to find what you're looking for.</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-sacli-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">📝</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Report</h3>
                <p class="text-gray-600">Register and report lost or found items with detailed descriptions and photos.</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-sacli-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">🤝</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Reunite</h3>
                <p class="text-gray-600">Connect with item owners or finders through our secure contact system.</p>
            </div>
        </div>
    </div>
</x-public-layout>