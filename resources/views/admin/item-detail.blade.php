<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Item Details') }} - {{ $item->title }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.items') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    Back to Items
                </a>
                @if($item->status === 'pending')
                    <button onclick="openVerificationModal({{ $item->id }}, 'approve')" 
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        Approve
                    </button>
                    <button onclick="openVerificationModal({{ $item->id }}, 'reject')" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        Reject
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Item Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Item Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $item->title }}</h3>
                                    <div class="flex items-center space-x-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            @if($item->type === 'lost') bg-red-100 text-red-800 @else bg-blue-100 text-blue-800 @endif">
                                            {{ ucfirst($item->type) }} Item
                                        </span>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            @if($item->status === 'verified') bg-emerald-100 text-emerald-800
                                            @elseif($item->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($item->status === 'rejected') bg-red-100 text-red-800
                                            @elseif($item->status === 'resolved') bg-gray-100 text-gray-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="prose max-w-none">
                                <h4 class="text-lg font-medium text-gray-900 mb-2">Description</h4>
                                <p class="text-gray-700 leading-relaxed">{{ $item->description }}</p>
                            </div>

                            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Item Details</h4>
                                    <dl class="space-y-2">
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-500">Category:</dt>
                                            <dd class="text-sm font-medium text-gray-900">{{ $item->category->name }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-500">Location:</dt>
                                            <dd class="text-sm font-medium text-gray-900">{{ $item->location }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-500">Date {{ $item->type === 'lost' ? 'Lost' : 'Found' }}:</dt>
                                            <dd class="text-sm font-medium text-gray-900">{{ $item->date_occurred->format('F j, Y') }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Submission Info</h4>
                                    <dl class="space-y-2">
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-500">Submitted by:</dt>
                                            <dd class="text-sm font-medium text-gray-900">{{ $item->user->name }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-500">Email:</dt>
                                            <dd class="text-sm font-medium text-gray-900">{{ $item->user->email }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-500">Submitted:</dt>
                                            <dd class="text-sm font-medium text-gray-900">{{ $item->created_at->format('M j, Y g:i A') }}</dd>
                                        </div>
                                        @if($item->verified_at)
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-500">Verified:</dt>
                                                <dd class="text-sm font-medium text-gray-900">{{ $item->verified_at->format('M j, Y g:i A') }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>

                            @if($item->contact_info)
                                <div class="mt-6">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Contact Information</h4>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        @php
                                            $contactInfo = is_string($item->contact_info) ? json_decode($item->contact_info, true) : $item->contact_info;
                                        @endphp
                                        @if(is_array($contactInfo))
                                            @foreach($contactInfo as $key => $value)
                                                @if($value)
                                                    <div class="flex justify-between py-1">
                                                        <span class="text-sm text-gray-500">{{ ucfirst($key) }}:</span>
                                                        <span class="text-sm font-medium text-gray-900">{{ $value }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <p class="text-sm text-gray-700">{{ $item->contact_info }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($item->admin_notes)
                                <div class="mt-6">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">Admin Notes</h4>
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <p class="text-sm text-gray-700">{{ $item->admin_notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Item Images -->
                    @if($item->images->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Images ({{ $item->images->count() }})</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach($item->images as $image)
                                        <div class="relative group">
                                            <img src="{{ Storage::url($image->filename) }}" 
                                                 alt="{{ $item->title }}" 
                                                 class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity duration-200"
                                                 onclick="openImageModal('{{ Storage::url($image->filename) }}', '{{ $item->title }}')">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                @if($item->status === 'pending')
                                    <button onclick="openVerificationModal({{ $item->id }}, 'approve')" 
                                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                        Approve Item
                                    </button>
                                    <button onclick="openVerificationModal({{ $item->id }}, 'reject')" 
                                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                        Reject Item
                                    </button>
                                @endif
                                
                                @if($item->status === 'verified')
                                    <a href="{{ route('items.show', $item->id) }}" target="_blank"
                                       class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out text-center block">
                                        View Public Page
                                    </a>
                                @endif
                                
                                <button onclick="contactUser()" 
                                        class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                    Contact User
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Similar Items -->
                    @if($similarItems->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Similar Items</h3>
                                <div class="space-y-3">
                                    @foreach($similarItems as $similarItem)
                                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                            @if($similarItem->images->count() > 0)
                                                <img src="{{ Storage::url($similarItem->images->first()->filename) }}" 
                                                     alt="{{ $similarItem->title }}" 
                                                     class="w-12 h-12 object-cover rounded-lg">
                                            @else
                                                <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $similarItem->title }}</p>
                                                <p class="text-xs text-gray-500">{{ $similarItem->category->name }} • {{ ucfirst($similarItem->type) }}</p>
                                            </div>
                                            <a href="{{ route('admin.items.show', $similarItem) }}" 
                                               class="text-emerald-600 hover:text-emerald-900 text-sm font-medium">
                                                View
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
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
                    <input type="hidden" id="item-id" name="item_id" value="{{ $item->id }}">
                    <input type="hidden" id="action" name="action">
                    
                    <div class="mb-4">
                        <label for="admin-notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Admin Notes (Optional)
                        </label>
                        <textarea id="admin-notes" name="admin_notes" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                                  placeholder="Add any notes about this decision...">{{ $item->admin_notes }}</textarea>
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

    <!-- Image Modal -->
    <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 max-w-4xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-white" id="image-modal-title">Image</h3>
                <button onclick="closeImageModal()" class="text-white hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <img id="modal-image" src="" alt="" class="max-w-full max-h-screen mx-auto rounded-lg">
        </div>
    </div>

    @push('scripts')
    <script>
        function openVerificationModal(itemId, action) {
            document.getElementById('action').value = action;
            
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
        
        function openImageModal(src, title) {
            document.getElementById('modal-image').src = src;
            document.getElementById('image-modal-title').textContent = title;
            document.getElementById('image-modal').classList.remove('hidden');
        }
        
        function closeImageModal() {
            document.getElementById('image-modal').classList.add('hidden');
        }
        
        function contactUser() {
            const email = '{{ $item->user->email }}';
            const subject = encodeURIComponent('Regarding your {{ $item->type }} item: {{ $item->title }}');
            window.location.href = `mailto:${email}?subject=${subject}`;
        }
        
        // Handle verification form submission
        document.getElementById('verification-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const action = formData.get('action');
            const adminNotes = formData.get('admin_notes');
            
            const confirmBtn = document.getElementById('confirm-action-btn');
            const originalText = confirmBtn.textContent;
            confirmBtn.textContent = 'Processing...';
            confirmBtn.disabled = true;
            
            fetch(`/admin/items/{{ $item->id }}/verify`, {
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
                    closeVerificationModal();
                    showNotification(data.message, 'success');
                    
                    // Reload the page to show updated status
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
            })
            .finally(() => {
                confirmBtn.textContent = originalText;
                confirmBtn.disabled = false;
            });
        });
        
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
        
        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeVerificationModal();
                closeImageModal();
            }
        });
    </script>
    @endpush
</x-app-layout>