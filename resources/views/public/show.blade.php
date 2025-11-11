<x-public-layout>
    <x-slot name="title">{{ $item->title ?? 'Item Details' }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}"
                        class="text-gray-700 hover:text-sacli-green-600 inline-flex items-center gap-2 transition-colors">
                        <x-icon name="home" size="sm" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-icon name="chevron-right" size="sm" class="text-gray-400" />
                        <a href="{{ route('search') }}"
                            class="ml-1 text-gray-700 hover:text-sacli-green-600 md:ml-2 transition-colors">Search</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <x-icon name="chevron-right" size="sm" class="text-gray-400" />
                        <span class="ml-1 text-gray-500 md:ml-2">{{ $item->title ?? 'Item Details' }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        @if (isset($item))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="lg:flex">
                    <!-- Image Gallery -->
                    <div class="lg:w-1/2">
                        @if ($item->images && $item->images->count() > 0)
                            <div class="relative">
                                <!-- Main Image -->
                                <div class="aspect-w-16 aspect-h-12 bg-gray-200">
                                    <img id="main-image" src="{{ $item->images->first()->url }}"
                                        alt="{{ $item->title }}" class="w-full h-96 object-cover">
                                </div>

                                <!-- Image Navigation -->
                                @if ($item->images->count() > 1)
                                    <div class="absolute inset-y-0 left-0 flex items-center">
                                        <button onclick="previousImage()"
                                            class="ml-2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition duration-150 ease-in-out">
                                            <x-icon name="chevron-left" size="md" />
                                        </button>
                                    </div>
                                    <div class="absolute inset-y-0 right-0 flex items-center">
                                        <button onclick="nextImage()"
                                            class="mr-2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition duration-150 ease-in-out">
                                            <x-icon name="chevron-right" size="md" />
                                        </button>
                                    </div>

                                    <!-- Image Counter -->
                                    <div
                                        class="absolute bottom-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                                        <span id="image-counter">1</span> / {{ $item->images->count() }}
                                    </div>
                                @endif
                            </div>

                            <!-- Thumbnail Gallery -->
                            @if ($item->images->count() > 1)
                                <div class="p-4 border-t border-gray-200">
                                    <div class="flex space-x-2 overflow-x-auto">
                                        @foreach ($item->images as $index => $image)
                                            <button onclick="showImage({{ $index }})"
                                                class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 {{ $index === 0 ? 'border-sacli-green-600' : 'border-gray-200' }} hover:border-sacli-green-600 transition duration-150 ease-in-out"
                                                data-thumbnail="{{ $index }}">
                                                <img src="{{ $image->url }}" alt="Thumbnail {{ $index + 1 }}"
                                                    class="w-full h-full object-cover">
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- No Image Placeholder -->
                            <div
                                class="h-96 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <div class="text-center">
                                    <x-icon name="photo" size="xl" class="text-gray-400 mx-auto mb-4" />
                                    <p class="text-gray-500">No images available</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Item Details -->
                    <div class="lg:w-1/2 p-6 lg:p-8">
                        <!-- Status and Category -->
                        <div class="flex items-center justify-between mb-4">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium {{ $item->type === 'found' ? 'bg-sacli-green-100 text-sacli-green-800' : 'bg-red-100 text-red-800' }}">
                                <x-icon :name="$item->type === 'found' ? 'check-circle' : 'exclamation-circle'" size="xs" />
                                {{ ucfirst($item->type) }} Item
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                <x-icon name="tag" size="xs" />
                                {{ $item->category->name ?? 'Other' }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $item->title }}</h1>

                        <!-- Description -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center gap-2">
                                <x-icon name="document-text" size="sm" class="text-sacli-green-600" />
                                Description
                            </h3>
                            <p class="text-gray-700 leading-relaxed">{{ $item->description }}</p>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1 flex items-center gap-2">
                                    <x-icon name="map-pin" size="sm" class="text-sacli-green-600" />
                                    Location
                                </h4>
                                <p class="text-gray-600 ml-6">
                                    {{ $item->location }}
                                </p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1 flex items-center gap-2">
                                    <x-icon name="calendar" size="sm" class="text-sacli-green-600" />
                                    Date {{ $item->type === 'lost' ? 'Lost' : 'Found' }}
                                </h4>
                                <p class="text-gray-600 ml-6">
                                    {{ $item->date_occurred ? $item->date_occurred->format('M j, Y') : 'Not specified' }}
                                </p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1 flex items-center gap-2">
                                    <x-icon name="clock" size="sm" class="text-sacli-green-600" />
                                    Duration
                                </h4>
                                <div class="ml-6">
                                    <x-duration-badge :item="$item" :showIcon="false" />
                                    <p class="text-xs text-gray-500 mt-1">
                                        Reported on {{ $item->created_at->format('M j, Y') }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1 flex items-center gap-2">
                                    <x-icon name="information-circle" size="sm" class="text-sacli-green-600" />
                                    Status
                                </h4>
                                <div class="ml-6">
                                    <x-status-badge :status="$item->status" />
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <x-icon name="user-circle" size="md" class="text-sacli-green-600" />
                                Contact Information
                            </h3>

                            <!-- Submitter Course and Year -->
                            @if ($item->user && ($item->user->course || $item->user->year))
                                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <x-icon name="academic-cap" size="sm"
                                            class="text-gray-400 flex-shrink-0" />
                                        <span>
                                            @if ($item->user->course)
                                                <span class="font-medium">{{ $item->user->course }}</span>
                                            @else
                                                <span class="text-gray-400">Course not specified</span>
                                            @endif
                                            @if ($item->user->year)
                                                <span class="text-gray-400 mx-1">•</span>
                                                <span
                                                    class="font-medium">{{ $item->user->year }}{{ $item->user->year == 1 ? 'st' : ($item->user->year == 2 ? 'nd' : ($item->user->year == 3 ? 'rd' : 'th')) }}
                                                    Year</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif

                            @if ($item->contact_info)
                                @php
                                    $contactInfo = is_string($item->contact_info)
                                        ? json_decode($item->contact_info, true)
                                        : $item->contact_info;
                                @endphp

                                <div class="space-y-3">
                                    @if (isset($contactInfo['email']))
                                        <div class="flex items-center">
                                            <x-icon name="envelope" size="md"
                                                class="text-gray-400 mr-3 flex-shrink-0" />
                                            <a href="mailto:{{ $contactInfo['email'] }}"
                                                class="text-sacli-green-600 hover:text-sacli-green-700 font-medium transition-colors">
                                                {{ $contactInfo['email'] }}
                                            </a>
                                        </div>
                                    @endif

                                    @if (isset($contactInfo['phone']))
                                        <div class="flex items-center">
                                            <x-icon name="phone" size="md"
                                                class="text-gray-400 mr-3 flex-shrink-0" />
                                            <a href="tel:{{ $contactInfo['phone'] }}"
                                                class="text-sacli-green-600 hover:text-sacli-green-700 font-medium transition-colors">
                                                {{ $contactInfo['phone'] }}
                                            </a>
                                        </div>
                                    @endif

                                    @if (isset($contactInfo['preferred_method']))
                                        <div class="flex items-center">
                                            <x-icon name="chat-bubble-left-right" size="md"
                                                class="text-gray-400 mr-3 flex-shrink-0" />
                                            <span class="text-gray-600">
                                                Preferred contact: <span
                                                    class="font-medium">{{ ucfirst($contactInfo['preferred_method']) }}</span>
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Contact Button -->
                                <div class="mt-6">
                                    <x-primary-button onclick="showContactModal()" class="w-full justify-center"
                                        icon="envelope">
                                        Contact {{ $item->type === 'lost' ? 'Owner' : 'Finder' }}
                                    </x-primary-button>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <x-icon name="exclamation-circle" size="lg"
                                        class="text-gray-400 mx-auto mb-2" />
                                    <p class="text-gray-500">Contact information not available</p>
                                </div>
                            @endif
                        </div>

                        <!-- Additional Actions -->
                        <div class="border-t border-gray-200 pt-6 mt-6">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <x-secondary-button onclick="shareItem()" class="flex-1 justify-center"
                                    icon="share">
                                    Share
                                </x-secondary-button>
                                <x-secondary-button onclick="reportItem()" class="flex-1 justify-center"
                                    icon="flag">
                                    Report Issue
                                </x-secondary-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Items -->
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <x-icon name="squares-2x2" size="md" class="text-sacli-green-600" />
                    Similar Items
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- This would be populated with related items -->
                    @for ($i = 1; $i <= 4; $i++)
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-200">
                            <div
                                class="h-32 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <x-icon name="photo" size="lg" class="text-gray-400" />
                            </div>
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-sacli-green-100 text-sacli-green-800">
                                        <x-icon name="check-circle" size="xs" />
                                        Found
                                    </span>
                                    <span class="text-xs text-gray-500 flex items-center gap-1">
                                        <x-icon name="clock" size="xs" />
                                        2 days ago
                                    </span>
                                </div>
                                <h3 class="font-medium text-gray-900 mb-1 text-sm">Similar Item {{ $i }}
                                </h3>
                                <p class="text-gray-600 text-xs mb-2">Brief description...</p>
                                <a href="#"
                                    class="text-sacli-green-600 hover:text-sacli-green-700 font-medium text-xs inline-flex items-center gap-1 transition-colors">
                                    View Details
                                    <x-icon name="arrow-right" size="xs" />
                                </a>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @else
            <!-- Item Not Found -->
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <x-icon name="document-magnifying-glass" size="xl" class="text-gray-400" />
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Item Not Found</h3>
                <p class="text-gray-600 mb-6">The item you're looking for doesn't exist or has been removed.</p>
                <x-primary-button href="{{ route('search') }}" icon="magnifying-glass">
                    Back to Search
                </x-primary-button>
            </div>
        @endif
    </div>

    <!-- Contact Modal -->
    <div id="contact-modal"
        class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-2xl rounded-2xl bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <x-icon name="envelope" size="md" class="text-sacli-green-600" />
                        Contact Information
                    </h3>
                    <button onclick="hideContactModal()"
                        class="text-gray-400 hover:text-gray-600 rounded-lg p-1 hover:bg-gray-100 transition-colors">
                        <x-icon name="x-mark" size="md" />
                    </button>
                </div>
                <div class="text-sm text-gray-600 mb-4">
                    Please use the contact information below to reach out about this item.
                </div>
                @if (isset($item) && $item->contact_info)
                    @php
                        $contactInfo = is_string($item->contact_info)
                            ? json_decode($item->contact_info, true)
                            : $item->contact_info;
                    @endphp
                    <div class="space-y-3">
                        @if (isset($contactInfo['email']))
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600 flex items-center gap-2">
                                    <x-icon name="envelope" size="sm" class="text-gray-400" />
                                    Email:
                                </span>
                                <a href="mailto:{{ $contactInfo['email'] }}"
                                    class="text-sacli-green-600 hover:text-sacli-green-700 font-medium text-sm transition-colors">
                                    {{ $contactInfo['email'] }}
                                </a>
                            </div>
                        @endif
                        @if (isset($contactInfo['phone']))
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600 flex items-center gap-2">
                                    <x-icon name="phone" size="sm" class="text-gray-400" />
                                    Phone:
                                </span>
                                <a href="tel:{{ $contactInfo['phone'] }}"
                                    class="text-sacli-green-600 hover:text-sacli-green-700 font-medium text-sm transition-colors">
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
                thumb.className = thumb.className.replace(/border-sacli-green-600|border-gray-200/g, '');
                thumb.className += i === index ? ' border-sacli-green-600' : ' border-gray-200';
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
                    title: '{{ $item->title ?? 'Item' }} - SACLI FOUNDIT',
                    text: '{{ $item->description ?? 'Check out this item' }}',
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
