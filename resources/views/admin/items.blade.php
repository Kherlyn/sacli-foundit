<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('All Items') }} ({{ $items->total() }})
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.items') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label for="query" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" id="query" name="query" value="{{ $filters['query'] }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="Search items...">
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ $filters['status'] === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="verified" {{ $filters['status'] === 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="rejected" {{ $filters['status'] === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="resolved" {{ $filters['status'] === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select id="type" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">All Types</option>
                                <option value="lost" {{ $filters['type'] === 'lost' ? 'selected' : '' }}>Lost</option>
                                <option value="found" {{ $filters['type'] === 'found' ? 'selected' : '' }}>Found</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select id="category_id" name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $filters['category_id'] == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Filter
                            </button>
                            <a href="{{ route('admin.items') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Items List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($items->count() > 0)
                        <div class="space-y-4">
                            @foreach($items as $item)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-start space-x-4">
                                        <!-- Item Image -->
                                        <div class="flex-shrink-0">
                                            @if($item->images->count() > 0)
                                                <img src="{{ Storage::url($item->images->first()->filename) }}" 
                                                     alt="{{ $item->title }}" 
                                                     class="w-16 h-16 object-cover rounded-lg">
                                            @else
                                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Item Details -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h3 class="text-lg font-medium text-gray-900 mb-1">{{ $item->title }}</h3>
                                                    <p class="text-gray-600 mb-2">{{ Str::limit($item->description, 100) }}</p>
                                                    
                                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                            @if($item->type === 'lost') bg-red-100 text-red-800 @else bg-blue-100 text-blue-800 @endif">
                                                            {{ ucfirst($item->type) }}
                                                        </span>
                                                        <span>{{ $item->category->name }}</span>
                                                        <span>{{ $item->location }}</span>
                                                        <span>{{ $item->date_occurred->format('M j, Y') }}</span>
                                                    </div>
                                                    
                                                    <div class="mt-2 text-sm text-gray-500">
                                                        <span>By {{ $item->user->name }}</span>
                                                        <span class="mx-2">•</span>
                                                        <span>{{ $item->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>

                                                <!-- Status and Actions -->
                                                <div class="flex-shrink-0 ml-4 text-right">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mb-2
                                                        @if($item->status === 'verified') bg-emerald-100 text-emerald-800
                                                        @elseif($item->status === 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($item->status === 'rejected') bg-red-100 text-red-800
                                                        @elseif($item->status === 'resolved') bg-gray-100 text-gray-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ ucfirst($item->status) }}
                                                    </span>
                                                    
                                                    <div class="flex space-x-2">
                                                        <a href="{{ route('admin.items.show', $item) }}" 
                                                           class="text-emerald-600 hover:text-emerald-900 text-sm font-medium">
                                                            View
                                                        </a>
                                                        @if($item->status === 'pending')
                                                            <button onclick="quickVerify({{ $item->id }}, 'approve')" 
                                                                    class="text-emerald-600 hover:text-emerald-900 text-sm font-medium">
                                                                Approve
                                                            </button>
                                                            <button onclick="quickVerify({{ $item->id }}, 'reject')" 
                                                                    class="text-red-600 hover:text-red-900 text-sm font-medium">
                                                                Reject
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $items->appends($filters)->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No items found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function quickVerify(itemId, action) {
            if (!confirm(`Are you sure you want to ${action} this item?`)) {
                return;
            }
            
            fetch(`/admin/items/${itemId}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    // Reload the page to reflect changes
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while processing the request', 'error');
            });
        }
        
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
                type === 'success' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-red-100 text-red-800 border border-red-200'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    </script>
    @endpush
</x-app-layout>