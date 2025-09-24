<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pending Items') }} ({{ $pendingItems->total() }})
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    Back to Dashboard
                </a>
                <button id="bulk-action-btn" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out disabled:opacity-50" disabled>
                    Bulk Actions
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Bulk Actions Bar -->
            <div id="bulk-actions-bar" class="hidden bg-emerald-50 border border-emerald-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-emerald-800">
                            <span id="selected-count">0</span> items selected
                        </span>
                        <div class="flex space-x-2">
                            <button onclick="bulkAction('approve')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1 rounded text-sm">
                                Approve Selected
                            </button>
                            <button onclick="bulkAction('reject')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                Reject Selected
                            </button>
                        </div>
                    </div>
                    <button onclick="clearSelection()" class="text-emerald-600 hover:text-emerald-800 text-sm">
                        Clear Selection
                    </button>
                </div>
            </div>

            <!-- Items List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($pendingItems->count() > 0)
                        <div class="space-y-6">
                            @foreach($pendingItems as $item)
                                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200" data-item-id="{{ $item->id }}">
                                    <div class="flex items-start space-x-4">
                                        <!-- Checkbox -->
                                        <div class="flex-shrink-0 pt-1">
                                            <input type="checkbox" class="item-checkbox rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" value="{{ $item->id }}">
                                        </div>

                                        <!-- Item Image -->
                                        <div class="flex-shrink-0">
                                            @if($item->images->count() > 0)
                                                <img src="{{ Storage::url($item->images->first()->filename) }}" 
                                                     alt="{{ $item->title }}" 
                                                     class="w-20 h-20 object-cover rounded-lg">
                                            @else
                                                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Item Details -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $item->title }}</h3>
                                                    <p class="text-gray-600 mb-3">{{ Str::limit($item->description, 150) }}</p>
                                                    
                                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                                        <div>
                                                            <span class="font-medium text-gray-500">Type:</span>
                                                            <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                                @if($item->type === 'lost') bg-red-100 text-red-800 @else bg-blue-100 text-blue-800 @endif">
                                                                {{ ucfirst($item->type) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <span class="font-medium text-gray-500">Category:</span>
                                                            <span class="ml-1 text-gray-900">{{ $item->category->name }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="font-medium text-gray-500">Location:</span>
                                                            <span class="ml-1 text-gray-900">{{ $item->location }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="font-medium text-gray-500">Date:</span>
                                                            <span class="ml-1 text-gray-900">{{ $item->date_occurred->format('M j, Y') }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 text-sm">
                                                        <span class="font-medium text-gray-500">Submitted by:</span>
                                                        <span class="ml-1 text-gray-900">{{ $item->user->name }} ({{ $item->user->email }})</span>
                                                        <span class="ml-2 text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="flex-shrink-0 ml-4">
                                                    <div class="flex space-x-2">
                                                        <button onclick="openVerificationModal({{ $item->id }}, 'approve')" 
                                                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-150 ease-in-out">
                                                            Approve
                                                        </button>
                                                        <button onclick="openVerificationModal({{ $item->id }}, 'reject')" 
                                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-150 ease-in-out">
                                                            Reject
                                                        </button>
                                                        <button onclick="viewItemDetails({{ $item->id }})" 
                                                                class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm font-medium transition duration-150 ease-in-out">
                                                            View Details
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
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No pending items</h3>
                            <p class="mt-1 text-sm text-gray-500">All items have been reviewed and processed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Modal -->
    <div id="verification-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modal-title">Verify Item</h3>
                    <button onclick="closeVerificationModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="verification-form">
                    <input type="hidden" id="item-id" name="item_id">
                    <input type="hidden" id="action" name="action">
                    
                    <div class="mb-4">
                        <label for="admin-notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Admin Notes (Optional)
                        </label>
                        <textarea id="admin-notes" name="admin_notes" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                                  placeholder="Add any notes about this decision..."></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeVerificationModal()" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition duration-150 ease-in-out">
                            Cancel
                        </button>
                        <button type="submit" id="confirm-action-btn"
                                class="px-4 py-2 text-sm font-medium text-white rounded-md transition duration-150 ease-in-out">
                            Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Item Details Modal -->
    <div id="item-details-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border max-w-4xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Item Details</h3>
                <button onclick="closeItemDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
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
            
            if (action === 'approve') {
                title.textContent = 'Approve Item';
                confirmBtn.textContent = 'Approve';
                confirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-md transition duration-150 ease-in-out';
            } else {
                title.textContent = 'Reject Item';
                confirmBtn.textContent = 'Reject';
                confirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition duration-150 ease-in-out';
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
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600 mx-auto"></div>
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
        }
        
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
                type === 'success' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-red-100 text-red-800 border border-red-200'
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
        
        function clearSelection() {
            selectedItems.clear();
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('bulk-actions-bar').classList.add('hidden');
            document.getElementById('bulk-action-btn').disabled = true;
        }
        
        function openVerificationModal(itemId, action) {
            console.log('openVerificationModal called with:', itemId, action);
            
            document.getElementById('item-id').value = itemId;
            document.getElementById('action').value = action;
            document.getElementById('admin-notes').value = '';
            
            const modal = document.getElementById('verification-modal');
            const title = document.getElementById('modal-title');
            const confirmBtn = document.getElementById('confirm-action-btn');
            
            if (action === 'approve') {
                title.textContent = 'Approve Item';
                confirmBtn.textContent = 'Approve';
                confirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-md transition duration-150 ease-in-out';
            } else {
                title.textContent = 'Reject Item';
                confirmBtn.textContent = 'Reject';
                confirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition duration-150 ease-in-out';
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
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600 mx-auto"></div>
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
        
        // Handle verification form submission
        document.getElementById('verification-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const itemId = formData.get('item_id');
            const action = formData.get('action');
            const adminNotes = formData.get('admin_notes');
            
            // Show loading state
            const confirmBtn = document.getElementById('confirm-action-btn');
            const originalText = confirmBtn.textContent;
            confirmBtn.textContent = 'Processing...';
            confirmBtn.disabled = true;
            
            // Make AJAX request
            fetch(`/admin/items/${itemId}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                confirmBtn.textContent = originalText;
                confirmBtn.disabled = false;
            });
        });
        
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
        
        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
                type === 'success' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-red-100 text-red-800 border border-red-200'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Remove after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    </script>
    @endpush
</x-app-layout>
   