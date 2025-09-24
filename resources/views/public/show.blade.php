<x-public-layout>
    <x-slot name="title">{{ $item->title ?? 'Item Details' }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-sacli-green-400 inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <a href="{{ route('search') }}" class="ml-1 text-gray-700 hover:text-sacli-green-400 md:ml-2">Search</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-1 text-gray-500 md:ml-2">{{ $item->title ?? 'Item Details' }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        @if(isset($item))
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="lg:flex">
                    <!-- Image Gallery -->
                    <div class="lg:w-1/2">
                        @if($item->images && $item->images->count() > 0)
                            <div class="relative">
                                <!-- Main Image -->
                                <div class="aspect-w-16 aspect-h-12 bg-gray-200">
                                    <img id="main-image" 
                                         src="{{ asset('storage/' . $item->images->first()->filename) }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-full h-96 object-cover">
                                </div>

                                <!-- Image Navigation -->
                                @if($item->images->count() > 1)
                                    <div class="absolute inset-y-0 left-0 flex items-center">
                                        <button onclick="previousImage()" 
                                                class="ml-2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition duration-150 ease-in-out">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="absolute inset-y-0 right-0 flex items-center">
                                        <button onclick="nextImage()" 
                                                class="mr-2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition duration-150 ease-in-out">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Image Counter -->
                                    <div class="absolute bottom-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                                        <span id="image-counter">1</span> / {{ $item->images->count() }}
                                    </div>
                                @endif
                            </div>

                            <!-- Thumbnail Gallery -->
                            @if($item->images->count() > 1)
                                <div class="p-4 border-t border-gray-200">
                                    <div class="flex space-x-2 overflow-x-auto">
                                        @foreach($item->images as $index => $image)
                                            <button onclick="showImage({{ $index }})" 
                                                    class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 {{ $index === 0 ? 'border-sacli-green-400' : 'border-gray-200' }} hover:border-sacli-green-400 transition duration-150 ease-in-out"
                                                    data-thumbnail="{{ $index }}">
                                                <img src="{{ asset('storage/' . $image->filename) }}" 
                                                     alt="Thumbnail {{ $index + 1 }}" 
                                                     class="w-full h-full object-cover">
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- No Image Placeholder -->
                            <div class="h-96 bg-gray-200 flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-gray-500">No images available</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Item Details -->
                    <div class="lg:w-1/2 p-6 lg:p-8">
                        <!-- Status and Category -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $item->type === 'found' ? 'bg-sacli-green-100 text-sacli-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($item->type) }} Item
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                {{ $item->category->name ?? 'Other' }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $item->title }}</h1>

                        <!-- Description -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                            <p class="text-gray-700 leading-relaxed">{{ $item->description }}</p>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Location</h4>
                                <p class="text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $item->location }}
                                </p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Date {{ $item->type === 'lost' ? 'Lost' : 'Found' }}</h4>
                                <p class="text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $item->date_occurred ? $item->date_occurred->format('M j, Y') : 'Not specified' }}
                                </p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Reported</h4>
                                <p class="text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $item->created_at->format('M j, Y') }} ({{ $item->created_at->diffForHumans() }})
                                </p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Status</h4>
                                <p class="text-gray-600 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ ucfirst($item->status) }}
                                </p>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                            
                            @if($item->contact_info)
                                @php
                                    $contactInfo = is_string($item->contact_info) ? json_decode($item->contact_info, true) : $item->contact_info;
                                @endphp
                                
                                <div class="space-y-3">
                                    @if(isset($contactInfo['email']))
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <a href="mailto:{{ $contactInfo['email'] }}" class="text-sacli-green-400 hover:text-sacli-green-500 font-medium">
                                                {{ $contactInfo['email'] }}
                                            </a>
                                        </div>
                                    @endif
                                    
                                    @if(isset($contactInfo['phone']))
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <a href="tel:{{ $contactInfo['phone'] }}" class="text-sacli-green-400 hover:text-sacli-green-500 font-medium">
                                                {{ $contactInfo['phone'] }}
                                            </a>
                                        </div>
                                    @endif
                                    
                                    @if(isset($contactInfo['preferred_method']))
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            <span class="text-gray-600">
                                                Preferred contact: <span class="font-medium">{{ ucfirst($contactInfo['preferred_method']) }}</span>
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Contact Button -->
                                <div class="mt-6">
                                    <button onclick="showContactModal()" 
                                            class="w-full bg-sacli-green-400 hover:bg-sacli-green-500 text-white py-3 px-6 rounded-lg font-semibold transition duration-150 ease-in-out flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Contact {{ $item->type === 'lost' ? 'Owner' : 'Finder' }}
                                    </button>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-gray-500">Contact information not available</p>
                                </div>
                            @endif
                        </div>

                        <!-- Additional Actions -->
                        <div class="border-t border-gray-200 pt-6 mt-6">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <button onclick="shareItem()" 
                                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg font-medium transition duration-150 ease-in-out flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                                    </svg>
                                    Share
                                </button>
                                <button onclick="reportItem()" 
                                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg font-medium transition duration-150 ease-in-out flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                    Report Issue
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Items -->
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Similar Items</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- This would be populated with related items -->
                    @for($i = 1; $i <= 4; $i++)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition duration-150 ease-in-out">
                            <div class="h-32 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-400 text-sm">No Image</span>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sacli-green-100 text-sacli-green-800">
                                        Found
                                    </span>
                                    <span class="text-xs text-gray-500">2 days ago</span>
                                </div>
                                <h3 class="font-medium text-gray-900 mb-1 text-sm">Similar Item {{ $i }}</h3>
                                <p class="text-gray-600 text-xs mb-2">Brief description...</p>
                                <a href="#" class="text-sacli-green-400 hover:text-sacli-green-500 font-medium text-xs">
                                    View Details →
                                </a>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @else
            <!-- Item Not Found -->
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Item Not Found</h3>
                <p class="text-gray-600 mb-6">The item you're looking for doesn't exist or has been removed.</p>
                <a href="{{ route('search') }}" class="bg-sacli-green-400 hover:bg-sacli-green-500 text-white px-6 py-2 rounded-lg font-medium transition duration-150 ease-in-out">
                    Back to Search
                </a>
            </div>
        @endif
    </div>

    <!-- Contact Modal -->
    <div id="contact-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
                    <button onclick="hideContactModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="text-sm text-gray-600 mb-4">
                    Please use the contact information below to reach out about this item.
                </div>
                @if(isset($item) && $item->contact_info)
                    @php
                        $contactInfo = is_string($item->contact_info) ? json_decode($item->contact_info, true) : $item->contact_info;
                    @endphp
                    <div class="space-y-3">
                        @if(isset($contactInfo['email']))
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Email:</span>
                                <a href="mailto:{{ $contactInfo['email'] }}" class="text-sacli-green-400 hover:text-sacli-green-500 font-medium text-sm">
                                    {{ $contactInfo['email'] }}
                                </a>
                            </div>
                        @endif
                        @if(isset($contactInfo['phone']))
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600">Phone:</span>
                                <a href="tel:{{ $contactInfo['phone'] }}" class="text-sacli-green-400 hover:text-sacli-green-500 font-medium text-sm">
                                    {{ $contactInfo['phone'] }}
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Image gallery functionality
        let currentImageIndex = 0;
        const images = @json($item->images ?? []);

        function showImage(index) {
            if (images.length === 0) return;
            
            currentImageIndex = index;
            const mainImage = document.getElementById('main-image');
            const counter = document.getElementById('image-counter');
            
            if (mainImage && images[index]) {
                mainImage.src = `/storage/${images[index].filename}`;
            }
            
            if (counter) {
                counter.textContent = index + 1;
            }
            
            // Update thumbnail borders
            document.querySelectorAll('[data-thumbnail]').forEach((thumb, i) => {
                thumb.className = thumb.className.replace(/border-sacli-green-400|border-gray-200/g, '');
                thumb.className += i === index ? ' border-sacli-green-400' : ' border-gray-200';
            });
        }

        function nextImage() {
            if (images.length === 0) return;
            currentImageIndex = (currentImageIndex + 1) % images.length;
            showImage(currentImageIndex);
        }

        function previousImage() {
            if (images.length === 0) return;
            currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
            showImage(currentImageIndex);
        }

        // Modal functionality
        function showContactModal() {
            document.getElementById('contact-modal').classList.remove('hidden');
        }

        function hideContactModal() {
            document.getElementById('contact-modal').classList.add('hidden');
        }

        // Share functionality
        function shareItem() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $item->title ?? "Item" }} - SACLI FOUNDIT',
                    text: '{{ $item->description ?? "Check out this item" }}',
                    url: window.location.href
                });
            } else {
                // Fallback to copying URL
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Link copied to clipboard!');
                });
            }
        }

        // Report functionality
        function reportItem() {
            alert('Report functionality would be implemented here');
        }

        // Keyboard navigation for image gallery
        document.addEventListener('keydown', function(e) {
            if (images.length > 1) {
                if (e.key === 'ArrowLeft') {
                    previousImage();
                } else if (e.key === 'ArrowRight') {
                    nextImage();
                }
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideContactModal();
            }
        });

        // Close modal on outside click
        document.getElementById('contact-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideContactModal();
            }
        });
    </script>
</x-public-layout>