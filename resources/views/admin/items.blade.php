<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <x-icon name="squares-2x2" size="md" class="text-sacli-green-600" />
                {{ __('All Items') }} ({{ $items->total() }})
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}"
                    class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out shadow-sm hover:shadow-md">
                    <x-icon name="arrow-left" size="sm" />
                    Back to Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.items') }}"
                        class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label for="query"
                                class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1.5">
                                <x-icon name="magnifying-glass" size="sm" class="text-gray-400" />
                                Search
                            </label>
                            <input type="text" id="query" name="query" value="{{ $filters['query'] }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sacli-green-500 focus:border-sacli-green-500 transition-all duration-200"
                                placeholder="Search items...">
                        </div>

                        <div>
                            <label for="status"
                                class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1.5">
                                <x-icon name="check-badge" size="sm" class="text-gray-400" />
                                Status
                            </label>
                            <select id="status" name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sacli-green-500 focus:border-sacli-green-500 transition-all duration-200">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ $filters['status'] === 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="verified" {{ $filters['status'] === 'verified' ? 'selected' : '' }}>
                                    Verified</option>
                                <option value="rejected" {{ $filters['status'] === 'rejected' ? 'selected' : '' }}>
                                    Rejected</option>
                                <option value="resolved" {{ $filters['status'] === 'resolved' ? 'selected' : '' }}>
                                    Resolved</option>
                            </select>
                        </div>

                        <div>
                            <label for="type"
                                class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1.5">
                                <x-icon name="tag" size="sm" class="text-gray-400" />
                                Type
                            </label>
                            <select id="type" name="type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sacli-green-500 focus:border-sacli-green-500 transition-all duration-200">
                                <option value="">All Types</option>
                                <option value="lost" {{ $filters['type'] === 'lost' ? 'selected' : '' }}>Lost</option>
                                <option value="found" {{ $filters['type'] === 'found' ? 'selected' : '' }}>Found
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="category_id"
                                class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1.5">
                                <x-icon name="folder" size="sm" class="text-gray-400" />
                                Category
                            </label>
                            <select id="category_id" name="category_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sacli-green-500 focus:border-sacli-green-500 transition-all duration-200">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $filters['category_id'] == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end space-x-2">
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-sacli-green-600 hover:bg-sacli-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out shadow-sm hover:shadow-md">
                                <x-icon name="funnel" size="sm" />
                                Filter
                            </button>
                            <a href="{{ route('admin.items') }}"
                                class="inline-flex items-center gap-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out">
                                <x-icon name="x-mark" size="sm" />
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Items List -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="p-6">
                    @if ($items->count() > 0)
                        <!-- Table with rounded corners and alternating row colors -->
                        <div class="overflow-hidden rounded-xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Item</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Details</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Submitted</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($items as $item)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-16 w-16">
                                                        @if ($item->images->count() > 0)
                                                            <img src="{{ $item->images->first()->url }}"
                                                                alt="{{ $item->title }}"
                                                                class="h-16 w-16 object-cover rounded-lg shadow-sm">
                                                        @else
                                                            <div
                                                                class="h-16 w-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                                                                <x-icon name="photo" size="lg"
                                                                    class="text-gray-400" />
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ Str::limit($item->title, 40) }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ Str::limit($item->description, 60) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="space-y-1">
                                                    <div class="flex items-center gap-1.5 text-sm">
                                                        <span
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                                            @if ($item->type === 'lost') bg-red-100 text-red-800 border border-red-200 @else bg-sacli-green-100 text-sacli-green-800 border border-sacli-green-200 @endif">
                                                            <x-icon :name="$item->type === 'lost' ? 'exclamation-circle' : 'check-circle'" size="xs" />
                                                            {{ ucfirst($item->type) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5 text-sm text-gray-600">
                                                        <x-icon name="tag" size="sm"
                                                            class="text-gray-400" />
                                                        {{ $item->category->name }}
                                                    </div>
                                                    <div class="flex items-center gap-1.5 text-sm text-gray-600">
                                                        <x-icon name="map-pin" size="sm"
                                                            class="text-gray-400" />
                                                        {{ Str::limit($item->location, 30) }}
                                                    </div>
                                                    <div class="flex items-center gap-1.5 text-sm text-gray-600">
                                                        <x-icon name="calendar" size="sm"
                                                            class="text-gray-400" />
                                                        {{ $item->date_occurred->format('M j, Y') }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-status-badge :status="$item->status" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex items-center gap-1.5 mb-1">
                                                    <x-icon name="user-circle" size="sm"
                                                        class="text-gray-400" />
                                                    {{ $item->user->name }}
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <x-icon name="clock" size="sm" class="text-gray-400" />
                                                    {{ $item->created_at->diffForHumans() }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end gap-2">
                                                    <!-- Icon-only action buttons with tooltips -->
                                                    <a href="{{ route('admin.items.show', $item) }}"
                                                        class="inline-flex items-center justify-center w-8 h-8 text-sacli-green-600 hover:text-white hover:bg-sacli-green-600 rounded-lg transition-all duration-200 group relative"
                                                        title="View Details">
                                                        <x-icon name="eye" size="sm" />
                                                        <span
                                                            class="absolute bottom-full mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">View
                                                            Details</span>
                                                    </a>
                                                    @if ($item->status === 'pending')
                                                        <button onclick="quickVerify({{ $item->id }}, 'approve')"
                                                            class="inline-flex items-center justify-center w-8 h-8 text-sacli-green-600 hover:text-white hover:bg-sacli-green-600 rounded-lg transition-all duration-200 group relative"
                                                            title="Approve">
                                                            <x-icon name="check-circle" size="sm" />
                                                            <span
                                                                class="absolute bottom-full mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">Approve</span>
                                                        </button>
                                                        <button onclick="quickVerify({{ $item->id }}, 'reject')"
                                                            class="inline-flex items-center justify-center w-8 h-8 text-red-600 hover:text-white hover:bg-red-600 rounded-lg transition-all duration-200 group relative"
                                                            title="Reject">
                                                            <x-icon name="x-circle" size="sm" />
                                                            <span
                                                                class="absolute bottom-full mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">Reject</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $items->appends($filters)->links() }}
                        </div>
                    @else
                        <x-empty-state icon="inbox" title="No items found"
                            message="Try adjusting your search filters to find items." />
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
                notification.className = `fixed top-4 right-4 p-4 rounded-xl shadow-lg z-50 ${
                type === 'success' ? 'bg-sacli-green-100 text-sacli-green-800 border border-sacli-green-200' : 'bg-red-100 text-red-800 border border-red-200'
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
