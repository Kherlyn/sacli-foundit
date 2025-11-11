<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <x-icon name="clock" size="md" class="text-yellow-600" />
                {{ __('Pending Items') }} ({{ $pendingItems->total() }})
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}"
                    class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out shadow-sm hover:shadow-md">
                    <x-icon name="arrow-left" size="sm" />
                    Back to Dashboard
                </a>
                <button id="bulk-action-btn"
                    class="inline-flex items-center gap-2 bg-sacli-green-600 hover:bg-sacli-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out shadow-sm hover:shadow-md disabled:opacity-50"
                    disabled>
                    <x-icon name="check-circle" size="sm" />
                    Bulk Actions
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Bulk Actions Bar -->
            <div id="bulk-actions-bar"
                class="hidden bg-sacli-green-50 border border-sacli-green-200 rounded-xl p-4 mb-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-sacli-green-800 flex items-center gap-2">
                            <x-icon name="check-badge" size="sm" class="text-sacli-green-600" />
                            <span id="selected-count">0</span> items selected
                        </span>
                        <div class="flex space-x-2">
                            <button onclick="bulkAction('approve')"
                                class="inline-flex items-center gap-1.5 bg-sacli-green-600 hover:bg-sacli-green-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                <x-icon name="check-circle" size="sm" />
                                Approve Selected
                            </button>
                            <button onclick="bulkAction('reject')"
                                class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                <x-icon name="x-circle" size="sm" />
                                Reject Selected
                            </button>
                        </div>
                    </div>
                    <button onclick="clearSelection()"
                        class="inline-flex items-center gap-1 text-sacli-green-600 hover:text-sacli-green-800 text-sm font-medium transition-colors">
                        <x-icon name="x-mark" size="sm" />
                        Clear Selection
                    </button>
                </div>
            </div>

            <!-- Items List -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="p-6">
                    @if ($pendingItems->count() > 0)
                        <div class="space-y-6">
                            @foreach ($pendingItems as $item)
                                <div class="border border-gray-200 rounded-xl p-6 hover:shadow-lg hover:border-sacli-green-300 transition-all duration-200"
                                    data-item-id="{{ $item->id }}">
                                    <div class="flex items-start space-x-4">
                                        <!-- Checkbox -->
                                        <div class="flex-shrink-0 pt-1">
                                            <input type="checkbox"
                                                class="item-checkbox rounded border-gray-300 text-sacli-green-600 focus:ring-sacli-green-500"
                                                value="{{ $item->id }}">
                                        </div>

                                        <!-- Item Image -->
                                        <div class="flex-shrink-0">
                                            @if ($item->images->count() > 0)
                                                <img src="{{ $item->images->first()->url }}" alt="{{ $item->title }}"
                                                    class="w-20 h-20 object-cover rounded-xl shadow-sm">
                                            @else
                                                <div
                                                    class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center">
                                                    <x-icon name="photo" size="xl" class="text-gray-400" />
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Item Details -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                                                        {{ $item->title }}</h3>
                                                    <p class="text-gray-600 mb-3">
                                                        {{ Str::limit($item->description, 150) }}</p>

                                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                                        <div>
                                                            <span class="font-medium text-gray-500">Type:</span>
                                                            <span
                                                                class="ml-1 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                                @if ($item->type === 'lost') bg-red-100 text-red-800 border border-red-200 @else bg-sacli-green-100 text-sacli-green-800 border border-sacli-green-200 @endif">
                                                                <x-icon :name="$item->type === 'lost' ? 'exclamation-circle' : 'check-circle'" size="xs" />
                                                                {{ ucfirst($item->type) }}
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center gap-1.5">
                                                            <x-icon name="tag" size="sm"
                                                                class="text-gray-400 flex-shrink-0" />
                                                            <span
                                                                class="text-gray-900">{{ $item->category->name }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-1.5">
                                                            <x-icon name="map-pin" size="sm"
                                                                class="text-gray-400 flex-shrink-0" />
                                                            <span
                                                                class="text-gray-900 truncate">{{ $item->location }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-1.5">
                                                            <x-icon name="calendar" size="sm"
                                                                class="text-gray-400 flex-shrink-0" />
                                                            <span
                                                                class="text-gray-900">{{ $item->date_occurred->format('M j, Y') }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 text-sm flex items-center gap-4 flex-wrap">
                                                        <div class="flex items-center gap-1.5">
                                                            <x-icon name="user-circle" size="sm"
                                                                class="text-gray-400 flex-shrink-0" />
                                                            <span class="text-gray-900">{{ $item->user->name }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-1.5">
                                                            <x-icon name="envelope" size="sm"
                                                                class="text-gray-400 flex-shrink-0" />
                                                            <span class="text-gray-600">{{ $item->user->email }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-1.5">
                                                            <x-duration-badge :item="$item" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="flex-shrink-0 ml-4">
                                                    <div class="flex space-x-2">
                                                        <button
                                                            onclick="openVerificationModal({{ $item->id }}, 'approve')"
                                                            class="inline-flex items-center gap-1.5 bg-sacli-green-600 hover:bg-sacli-green-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                                            <x-icon name="check-circle" size="sm" />
                                                            Approve
                                                        </button>
                                                        <button
                                                            onclick="openVerificationModal({{ $item->id }}, 'reject')"
                                                            class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                                            <x-icon name="x-circle" size="sm" />
                                                            Reject
                                                        </button>
                                                        <button onclick="viewItemDetails({{ $item->id }})"
                                                            class="inline-flex items-center gap-1.5 bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                                            <x-icon name="eye" size="sm" />
                                                            View
                                                        </button>
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
                            {{ $pendingItems->links() }}
                        </div>
                    @else
                        <x-empty-state icon="check-badge" title="No pending items"
                            message="All items have been reviewed and processed. Great job!" />
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Modal -->
    <div id="verification-modal"
        class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border-0 w-96 shadow-2xl rounded-2xl bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div id="modal-icon-container" class="w-10 h-10 rounded-lg flex items-center justify-center">
                            <!-- Icon will be set dynamically -->
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Verify Item</h3>
                    </div>
                    <button onclick="closeVerificationModal()"
                        class="text-gray-400 hover:text-gray-600 transition-colors rounded-lg p-1 hover:bg-gray-100">
                        <x-icon name="x-mark" size="md" />
                    </button>
                </div>

                <form id="verification-form">
                    <input type="hidden" id="item-id" name="item_id">
                    <input type="hidden" id="action" name="action">

                    <div class="mb-4">
                        <label for="admin-notes"
                            class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-1.5">
                            <x-icon name="document-text" size="sm" class="text-gray-400" />
                            Admin Notes (Optional)
                        </label>
                        <textarea id="admin-notes" name="admin_notes" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sacli-green-500 focus:border-sacli-green-500 transition-all duration-200"
                            placeholder="Add any notes about this decision..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeVerificationModal()"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">
                            <x-icon name="x-mark" size="sm" />
                            Cancel
                        </button>
                        <button type="submit" id="confirm-action-btn"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                            <span id="confirm-icon-container"></span>
                            <span id="confirm-text">Confirm</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Item Details Modal -->
    <div id="item-details-modal"
        class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border-0 max-w-4xl shadow-2xl rounded-2xl bg-white">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-sacli-green-100 rounded-lg flex items-center justify-center">
                        <x-icon name="document-text" size="md" class="text-sacli-green-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Item Details</h3>
                </div>
                <button onclick="closeItemDetailsModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors rounded-lg p-1 hover:bg-gray-100">
                    <x-icon name="x-mark" size="md" />
                </button>
            </div>
            <div id="item-details-content">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            console.log('Admin pending items script loaded');
            let selectedItems = new Set();

            // Function declarations
            function openVerificationModal(itemId, action) {
                console.log('openVerificationModal called with:', itemId, action);

                document.getElementById('item-id').value = itemId;
                document.getElementById('action').value = action;
                document.getElementById('admin-notes').value = '';

                const modal = document.getElementById('verification-modal');
                const title = document.getElementById('modal-title');
                const confirmBtn = document.getElementById('confirm-action-btn');
                const iconContainer = document.getElementById('modal-icon-container');
                const confirmIconContainer = document.getElementById('confirm-icon-container');
                const confirmText = document.getElementById('confirm-text');

                if (action === 'approve') {
                    title.textContent = 'Approve Item';
                    confirmText.textContent = 'Approve';
                    confirmBtn.className =
                        'inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-sacli-green-600 hover:bg-sacli-green-700 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md';
                    iconContainer.className = 'w-10 h-10 rounded-lg bg-sacli-green-100 flex items-center justify-center';
                    iconContainer.innerHTML =
                        '<svg class="w-6 h-6 text-sacli-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                    confirmIconContainer.innerHTML =
                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                } else {
                    title.textContent = 'Reject Item';
                    confirmText.textContent = 'Reject';
                    confirmBtn.className =
                        'inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md';
                    iconContainer.className = 'w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center';
                    iconContainer.innerHTML =
                        '<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                    confirmIconContainer.innerHTML =
                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                }

                modal.classList.remove('hidden');
            }

            function closeVerificationModal() {
                document.getElementById('verification-modal').classList.add('hidden');
            }

            function viewItemDetails(itemId) {
                // This would typically load item details via AJAX
                // For now, we'll show a placeholder
                document.getElementById('item-details-content').innerHTML = `
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sacli-green-600 mx-auto"></div>
                    <p class="mt-2 text-gray-500">Loading item details...</p>
                </div>
            `;
                document.getElementById('item-details-modal').classList.remove('hidden');

                // Simulate loading - in real implementation, this would be an AJAX call
                setTimeout(() => {
                    document.getElementById('item-details-content').innerHTML = `
                    <p class="text-gray-600">Detailed view for item #${itemId} would be loaded here via AJAX.</p>
                `;
                }, 1000);
            }

            function closeItemDetailsModal() {
                document.getElementById('item-details-modal').classList.add('hidden');
            }

            function bulkAction(action) {
                if (selectedItems.size === 0) return;

                const itemIds = Array.from(selectedItems);
                const actionText = action === 'approve' ? 'approve' : 'reject';

                if (!confirm(`Are you sure you want to ${actionText} ${itemIds.length} selected items?`)) {
                    return;
                }

                fetch('/admin/items/bulk-action', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            action: action,
                            item_ids: itemIds
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove processed items from the list
                            itemIds.forEach(itemId => {
                                const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
                                if (itemElement) {
                                    itemElement.style.opacity = '0.5';
                                    setTimeout(() => {
                                        itemElement.remove();
                                    }, 500);
                                }
                            });

                            clearSelection();
                            showNotification(data.message, 'success');
                        } else {
                            showNotification(data.message || 'An error occurred', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred while processing the bulk action', 'error');
                    });
            }

            function clearSelection() {
                selectedItems.clear();
                document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
                document.getElementById('bulk-actions-bar').classList.add('hidden');
                document.getElementById('bulk-action-btn').disabled = true;
                document.getElementById('selected-count').textContent = '0';
            }

            function showNotification(message, type) {
                // Create notification element
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 p-4 rounded-xl shadow-lg z-50 ${
                type === 'success' ? 'bg-sacli-green-100 text-sacli-green-800 border border-sacli-green-200' : 'bg-red-100 text-red-800 border border-red-200'
            }`;
                notification.textContent = message;

                document.body.appendChild(notification);

                // Remove after 5 seconds
                setTimeout(() => {
                    notification.remove();
                }, 5000);
            }

            // Make functions globally accessible
            window.openVerificationModal = openVerificationModal;
            window.closeVerificationModal = closeVerificationModal;
            window.viewItemDetails = viewItemDetails;
            window.closeItemDetailsModal = closeItemDetailsModal;
            window.bulkAction = bulkAction;
            window.clearSelection = clearSelection;

            // Handle checkbox selection
            document.addEventListener('DOMContentLoaded', function() {
                const checkboxes = document.querySelectorAll('.item-checkbox');
                const bulkActionBtn = document.getElementById('bulk-action-btn');
                const bulkActionsBar = document.getElementById('bulk-actions-bar');
                const selectedCountSpan = document.getElementById('selected-count');

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            selectedItems.add(this.value);
                        } else {
                            selectedItems.delete(this.value);
                        }

                        updateBulkActionsUI();
                    });
                });

                function updateBulkActionsUI() {
                    const count = selectedItems.size;
                    selectedCountSpan.textContent = count;

                    if (count > 0) {
                        bulkActionBtn.disabled = false;
                        bulkActionsBar.classList.remove('hidden');
                    } else {
                        bulkActionBtn.disabled = true;
                        bulkActionsBar.classList.add('hidden');
                    }
                }
            });

            // Handle verification form submission
            document.getElementById('verification-form').addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const itemId = formData.get('item_id');
                const action = formData.get('action');
                const adminNotes = formData.get('admin_notes');

                // Show loading state
                const confirmBtn = document.getElementById('confirm-action-btn');
                const confirmText = document.getElementById('confirm-text');
                const originalText = confirmText.textContent;
                confirmText.textContent = 'Processing...';
                confirmBtn.disabled = true;

                // Make AJAX request
                fetch(`/admin/items/${itemId}/verify`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            action: action,
                            admin_notes: adminNotes
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the item from the list or update its status
                            const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
                            if (itemElement) {
                                itemElement.style.opacity = '0.5';
                                setTimeout(() => {
                                    itemElement.remove();
                                }, 500);
                            }

                            closeVerificationModal();

                            // Show success message
                            showNotification(data.message, 'success');
                        } else {
                            showNotification(data.message || 'An error occurred', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred while processing the request', 'error');
                    })
                    .finally(() => {
                        confirmText.textContent = originalText;
                        confirmBtn.disabled = false;
                    });
            });
        </script>
    @endpush
</x-app-layout>
