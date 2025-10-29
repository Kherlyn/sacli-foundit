<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $item->title }}
            </h2>
            <div class="flex items-center space-x-3">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if ($item->type === 'lost') bg-red-100 text-red-800 @else bg-emerald-100 text-emerald-800 @endif">
                    {{ ucfirst($item->type) }} Item
                </span>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if ($item->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($item->status === 'verified') bg-green-100 text-green-800
                    @elseif($item->status === 'rejected') bg-red-100 text-red-800
                    @else bg-blue-100 text-blue-800 @endif">
                    {{ ucfirst($item->status) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Item Images -->
                    @if ($item->images->count() > 0)
                        <div class="mb-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($item->images as $image)
                                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                                        <img src="{{ $image->url }}" alt="{{ $item->title }}"
                                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-200 cursor-pointer"
                                            onclick="openImageModal('{{ $image->url }}')">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Item Details -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Main Content -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Description -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Description</h3>
                                <p class="text-gray-700 leading-relaxed">{{ $item->description }}</p>
                            </div>

                            <!-- Location and Date -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">
                                        {{ $item->type === 'lost' ? 'Lost Location' : 'Found Location' }}
                                    </h4>
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $item->location }}
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">
                                        {{ $item->type === 'lost' ? 'Date Lost' : 'Date Found' }}
                                    </h4>
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $item->date_occurred->format('F j, Y') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Admin Notes (if any) -->
                            @if ($item->admin_notes)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Admin Notes</h4>
                                    <p class="text-gray-700 text-sm">{{ $item->admin_notes }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Sidebar -->
                        <div class="space-y-6">
                            <!-- Item Info Card -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Item Information</h3>

                                <div class="space-y-3">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Category:</span>
                                        <span class="ml-2 text-sm text-gray-900">{{ $item->category->name }}</span>
                                    </div>

                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Reference ID:</span>
                                        <span class="ml-2 text-sm font-mono text-gray-900">#{{ $item->id }}</span>
                                    </div>

                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Submitted:</span>
                                        <span
                                            class="ml-2 text-sm text-gray-900">{{ $item->created_at->format('M j, Y \a\t g:i A') }}</span>
                                    </div>

                                    @if ($item->verified_at)
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Verified:</span>
                                            <span
                                                class="ml-2 text-sm text-gray-900">{{ $item->verified_at->format('M j, Y \a\t g:i A') }}</span>
                                        </div>
                                    @endif

                                    @if ($item->resolved_at)
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Resolved:</span>
                                            <span
                                                class="ml-2 text-sm text-gray-900">{{ $item->resolved_at->format('M j, Y \a\t g:i A') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Contact Information -->
                            @if ($item->isVerified() || $item->user_id === auth()->id())
                                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-emerald-800 mb-4">Contact Information</h3>

                                    @php
                                        $contactInfo = $item->contact_info;
                                        $method = $contactInfo['method'] ?? 'email';
                                    @endphp

                                    <div class="space-y-2">
                                        @if (in_array($method, ['email', 'both']) && !empty($contactInfo['email']))
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-emerald-600 mr-2" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                <a href="mailto:{{ $contactInfo['email'] }}"
                                                    class="text-emerald-700 hover:text-emerald-800 text-sm">
                                                    {{ $contactInfo['email'] }}
                                                </a>
                                            </div>
                                        @endif

                                        @if (in_array($method, ['phone', 'both']) && !empty($contactInfo['phone']))
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-emerald-600 mr-2" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                <a href="tel:{{ $contactInfo['phone'] }}"
                                                    class="text-emerald-700 hover:text-emerald-800 text-sm">
                                                    {{ $contactInfo['phone'] }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Actions -->
                            @if ($item->user_id === auth()->id())
                                <div class="space-y-3">
                                    @if ($item->status === 'pending')
                                        <a href="{{ route('items.edit', $item) }}"
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium text-center block transition duration-150 ease-in-out">
                                            Edit Item
                                        </a>

                                        <form action="{{ route('items.destroy', $item) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this item? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                                Delete Item
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('items.my-items') }}"
                                        class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium text-center block transition duration-150 ease-in-out">
                                        Back to My Items
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="max-w-4xl max-h-full p-4">
            <img id="modalImage" src="" alt="Item image" class="max-w-full max-h-full object-contain">
            <button onclick="closeImageModal()"
                class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
                ×
            </button>
        </div>
    </div>

    <script>
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // Close modal when clicking outside the image
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</x-app-layout>
