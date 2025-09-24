<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Items') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('items.create', ['type' => 'lost']) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Report Lost Item
                </a>
                <a href="{{ route('items.create', ['type' => 'found']) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Report Found Item
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                    @if($items->count() > 0)
                        <!-- Items Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($items as $item)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition duration-150 ease-in-out">
                                    <!-- Item Image -->
                                    <div class="h-48 bg-gray-100 rounded-t-lg overflow-hidden">
                                        @if($item->images->count() > 0)
                                            <img src="{{ Storage::url('items/' . $item->id . '/' . $item->images->first()->filename) }}" 
                                                 alt="{{ $item->title }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Item Content -->
                                    <div class="p-4">
                                        <!-- Status and Type -->
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($item->type === 'lost') bg-red-100 text-red-800 @else bg-emerald-100 text-emerald-800 @endif">
                                                {{ ucfirst($item->type) }}
                                            </span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($item->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($item->status === 'verified') bg-green-100 text-green-800
                                                @elseif($item->status === 'rejected') bg-red-100 text-red-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </div>

                                        <!-- Title and Category -->
                                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $item->title }}</h3>
                                        <p class="text-sm text-gray-600 mb-2">{{ $item->category->name }}</p>

                                        <!-- Description -->
                                        <p class="text-gray-700 text-sm mb-3 line-clamp-2">{{ $item->description }}</p>

                                        <!-- Location and Date -->
                                        <div class="text-xs text-gray-500 mb-4 space-y-1">
                                            <div class="flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ $item->location }}
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $item->date_occurred->format('M j, Y') }}
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Submitted {{ $item->created_at->diffForHumans() }}
                                            </div>
                                        </div>

                                        <!-- Reference Number -->
                                        <div class="text-xs text-gray-500 mb-4 font-mono bg-gray-50 px-2 py-1 rounded">
                                            Ref: #{{ $item->id }}
                                        </div>

                                        <!-- Admin Notes (if rejected) -->
                                        @if($item->status === 'rejected' && $item->admin_notes)
                                            <div class="bg-red-50 border border-red-200 rounded-md p-3 mb-4">
                                                <p class="text-xs font-medium text-red-800 mb-1">Admin Notes:</p>
                                                <p class="text-xs text-red-700">{{ $item->admin_notes }}</p>
                                            </div>
                                        @endif

                                        <!-- Action Buttons -->
                                        <div class="flex items-center justify-between">
                                            <a href="{{ route('items.view', $item) }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                                                View Details →
                                            </a>
                                            
                                            <div class="flex space-x-2">
                                                @if($item->status === 'pending')
                                                    <a href="{{ route('items.edit', $item) }}" class="text-blue-600 hover:text-blue-700 text-xs font-medium">
                                                        Edit
                                                    </a>
                                                    <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline" 
                                                          onsubmit="return confirm('Are you sure you want to delete this item?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $items->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No items yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by reporting a lost or found item.</p>
                            <div class="mt-6 flex justify-center space-x-3">
                                <a href="{{ route('items.create', ['type' => 'lost']) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                    Report Lost Item
                                </a>
                                <a href="{{ route('items.create', ['type' => 'found']) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                                    Report Found Item
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>