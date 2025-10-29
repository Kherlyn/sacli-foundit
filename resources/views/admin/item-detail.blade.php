<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <x-icon name="document-text" size="md" class="text-sacli-green-600" />
                {{ __('Item Details') }} - {{ $item->title }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('admin.items') }}"
                    class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out shadow-sm hover:shadow-md">
                    <x-icon name="arrow-left" size="sm" />
                    Back to Items
                </a>
                @if ($item->status === 'pending')
                    <button onclick="openVerificationModal({{ $item->id }}, 'approve')"
                        class="inline-flex items-center gap-2 bg-sacli-green-600 hover:bg-sacli-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out shadow-sm hover:shadow-md">
                        <x-icon name="check-circle" size="sm" />
                        Approve
                    </button>
                    <button onclick="openVerificationModal({{ $item->id }}, 'reject')"
                        class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out shadow-sm hover:shadow-md">
                        <x-icon name="x-circle" size="sm" />
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
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $item->title }}</h3>
                                    <div class="flex items-center space-x-4">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold border
                                            @if ($item->type === 'lost') bg-red-100 text-red-800 border-red-200 @else bg-sacli-green-100 text-sacli-green-800 border-sacli-green-200 @endif">
                                            <x-icon :name="$item->type === 'lost' ? 'exclamation-circle' : 'check-circle'" size="xs" />
                                            {{ ucfirst($item->type) }} Item
                                        </span>
                                        <x-status-badge :status="$item->status" />
                                    </div>
                                </div>
                            </div>

                            <div class="prose max-w-none">
                                <h4 class="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2">
                                    <x-icon name="document-text" size="sm" class="text-gray-400" />
                                    Description
                                </h4>
                                <p class="text-gray-700 leading-relaxed">{{ $item->description }}</p>
                            </div>

                            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4
                                        class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-1.5">
                                        <x-icon name="information-circle" size="sm" class="text-gray-400" />
                                        Item Details
                                    </h4>
                                    <dl class="space-y-3">
                                        <div class="flex items-center gap-2">
                                            <x-icon name="tag" size="sm" class="text-gray-400 flex-shrink-0" />
                                            <dt class="text-sm text-gray-500">Category:</dt>
                                            <dd class="text-sm font-medium text-gray-900 ml-auto">
                                                {{ $item->category->name }}</dd>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <x-icon name="map-pin" size="sm" class="text-gray-400 flex-shrink-0" />
                                            <dt class="text-sm text-gray-500">Location:</dt>
                                            <dd class="text-sm font-medium text-gray-900 ml-auto">{{ $item->location }}
                                            </dd>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <x-icon name="calendar" size="sm"
                                                class="text-gray-400 flex-shrink-0" />
                                            <dt class="text-sm text-gray-500">Date
                                                {{ $item->type === 'lost' ? 'Lost' : 'Found' }}:</dt>
                                            <dd class="text-sm font-medium text-gray-900 ml-auto">
                                                {{ $item->date_occurred->format('F j, Y') }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <div>
                                    <h4
                                        class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-1.5">
                                        <x-icon name="user-circle" size="sm" class="text-gray-400" />
                                        Submission Info
                                    </h4>
                                    <dl class="space-y-3">
                                        <div class="flex items-center gap-2">
                                            <x-icon name="user" size="sm" class="text-gray-400 flex-shrink-0" />
                                            <dt class="text-sm text-gray-500">Submitted by:</dt>
                                            <dd class="text-sm font-medium text-gray-900 ml-auto">
                                                {{ $item->user->name }}</dd>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <x-icon name="envelope" size="sm"
                                                class="text-gray-400 flex-shrink-0" />
                                            <dt class="text-sm text-gray-500">Email:</dt>
                                            <dd class="text-sm font-medium text-gray-900 ml-auto">
                                                {{ $item->user->email }}</dd>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <x-icon name="clock" size="sm" class="text-gray-400 flex-shrink-0" />
                                            <dt class="text-sm text-gray-500">Submitted:</dt>
                                            <dd class="text-sm font-medium text-gray-900 ml-auto">
                                                {{ $item->created_at->format('M j, Y g:i A') }}</dd>
                                        </div>
                                        @if ($item->verified_at)
                                            <div class="flex items-center gap-2">
                                                <x-icon name="check-badge" size="sm"
                                                    class="text-gray-400 flex-shrink-0" />
                                                <dt class="text-sm text-gray-500">Verified:</dt>
                                                <dd class="text-sm font-medium text-gray-900 ml-auto">
                                                    {{ $item->verified_at->format('M j, Y g:i A') }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>

                            @if ($item->contact_info)
                                <div class="mt-6">
                                    <h4
                                        class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-1.5">
                                        <x-icon name="phone" size="sm" class="text-gray-400" />
                                        Contact Information
                                    </h4>
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        @php
                                            $contactInfo = is_string($item->contact_info)
                                                ? json_decode($item->contact_info, true)
                                                : $item->contact_info;
                                        @endphp
                                        @if (is_array($contactInfo))
                                            <div class="space-y-2">
                                                @foreach ($contactInfo as $key => $value)
                                                    @if ($value)
                                                        <div class="flex items-center gap-2">
                                                            <x-icon name="at-symbol" size="sm"
                                                                class="text-gray-400 flex-shrink-0" />
                                                            <span
                                                                class="text-sm text-gray-500">{{ ucfirst($key) }}:</span>
                                                            <span
                                                                class="text-sm font-medium text-gray-900 ml-auto">{{ $value }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-700">{{ $item->contact_info }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if ($item->admin_notes)
                                <div class="mt-6">
                                    <h4
                                        class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-1.5">
                                        <x-icon name="document-text" size="sm" class="text-gray-400" />
                                        Admin Notes
                                    </h4>
                                    <div
                                        class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-start gap-2">
                                        <x-icon name="exclamation-triangle" size="sm"
                                            class="text-yellow-600 flex-shrink-0 mt-0.5" />
                                        <p class="text-sm text-gray-700">{{ $item->admin_notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Item Images -->
                    @if ($item->images->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <x-icon name="photo" size="md" class="text-gray-400" />
                                    Images ({{ $item->images->count() }})
                                </h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach ($item->images as $image)
                                        <div class="relative group">
                                            <img src="{{ $image->url }}" alt="{{ $item->title }}"
                                                class="w-full h-32 object-cover rounded-xl cursor-pointer hover:opacity-75 transition-all duration-200 shadow-sm hover:shadow-md"
                                                onclick="openImageModal('{{ $image->url }}', '{{ $item->title }}')">
                                            <div
                                                class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded-xl flex items-center justify-center">
                                                <x-icon name="magnifying-glass-plus" size="xl"
                                                    class="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" />
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
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                <x-icon name="bolt" size="md" class="text-gray-400" />
                                Quick Actions
                            </h3>
                            <div class="space-y-3">
                                @if ($item->status === 'pending')
                                    <button onclick="openVerificationModal({{ $item->id }}, 'approve')"
                                        class="w-full inline-flex items-center justify-center gap-2 bg-sacli-green-600 hover:bg-sacli-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                        <x-icon name="check-circle" size="sm" />
                                        Approve Item
                                    </button>
                                    <button onclick="openVerificationModal({{ $item->id }}, 'reject')"
                                        class="w-full inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                        <x-icon name="x-circle" size="sm" />
                                        Reject Item
                                    </button>
                                @endif

                                @if ($item->status === 'verified')
                                    <a href="{{ route('items.show', $item->id) }}" target="_blank"
                                        class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                        <x-icon name="arrow-top-right-on-square" size="sm" />
                                        View Public Page
                                    </a>
                                @endif

                                <button onclick="contactUser()"
                                    class="w-full inline-flex items-center justify-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                    <x-icon name="envelope" size="sm" />
                                    Contact User
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Similar Items -->
                    @if ($similarItems->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                    <x-icon name="squares-2x2" size="md" class="text-gray-400" />
                                    Similar Items
                                </h3>
                                <div class="space-y-3">
                                    @foreach ($similarItems as $similarItem)
                                        <div
                                            class="flex items-center space-x-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                                            @if ($similarItem->images->count() > 0)
                                                <img src="{{ $similarItem->images->first()->url }}"
                                                    alt="{{ $similarItem->title }}"
                                                    class="w-12 h-12 object-cover rounded-lg shadow-sm">
                                            @else
                                                <div
                                                    class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <x-icon name="photo" size="md" class="text-gray-400" />
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $similarItem->title }}</p>
                                                <p class="text-xs text-gray-500 flex items-center gap-1">
                                                    <x-icon name="tag" size="xs" class="text-gray-400" />
                                                    {{ $similarItem->category->name }} •
                                                    {{ ucfirst($similarItem->type) }}
                                                </p>
                                            </div>
                                            <a href="{{ route('admin.items.show', $similarItem) }}"
                                                class="inline-flex items-center justify-center w-8 h-8 text-sacli-green-600 hover:text-white hover:bg-sacli-green-600 rounded-lg transition-all duration-200">
                                                <x-icon name="arrow-right" size="sm" />
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
                    <input type="hidden" id="item-id" name="item_id" value="{{ $item->id }}">
                    <input type="hidden" id="action" name="action">

                    <div class="mb-4">
                        <label for="admin-notes"
                            class="block text-sm font-medium text-gray-700 mb-2 flex items-center gap-1.5">
                            <x-icon name="document-text" size="sm" class="text-gray-400" />
                            Admin Notes (Optional)
                        </label>
                        <textarea id="admin-notes" name="admin_notes" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sacli-green-500 focus:border-sacli-green-500 transition-all duration-200"
                            placeholder="Add any notes about this decision...">{{ $item->admin_notes }}</textarea>
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

    <!-- Image Modal -->
    <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 max-w-4xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-white flex items-center gap-2" id="image-modal-title">
                    <x-icon name="photo" size="md" />
                    Image
                </h3>
                <button onclick="closeImageModal()" class="text-white hover:text-gray-300 transition-colors">
                    <x-icon name="x-mark" size="lg" />
                </button>
            </div>
            <img id="modal-image" src="" alt=""
                class="max-w-full max-h-screen mx-auto rounded-xl shadow-2xl">
        </div>
    </div>

    @push('scripts')
        <script>
            function openVerificationModal(itemId, action) {
                document.getElementById('action').value = action;

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

            function openImageModal(src, title) {
                document.getElementById('modal-image').src = src;
                document.getElementById('image-modal-title').innerHTML = `
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    ${title}
                `;
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
                const confirmText = document.getElementById('confirm-text');
                const originalText = confirmText.textContent;
                confirmText.textContent = 'Processing...';
                confirmBtn.disabled = true;

                fetch(`/admin/items/{{ $item->id }}/verify`, {
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
                        confirmText.textContent = originalText;
                        confirmBtn.disabled = false;
                    });
            });

            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 p-4 rounded-xl shadow-lg z-50 flex items-center gap-2 ${
                type === 'success' ? 'bg-sacli-green-100 text-sacli-green-800 border border-sacli-green-200' : 'bg-red-100 text-red-800 border border-red-200'
            }`;

                const icon = type === 'success' ?
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

                notification.innerHTML = icon + '<span>' + message + '</span>';
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
