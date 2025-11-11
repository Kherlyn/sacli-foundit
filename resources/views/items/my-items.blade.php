<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <x-icon name="folder" size="md" class="text-sacli-green-600" />
                {{ __('My Items') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('items.create', ['type' => 'lost']) }}"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 inline-flex items-center gap-2 shadow-sm hover:shadow-md">
                    <x-icon name="exclamation-circle" size="sm" />
                    Report Lost Item
                </a>
                <a href="{{ route('items.create', ['type' => 'found']) }}"
                    class="bg-sacli-green-600 hover:bg-sacli-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 inline-flex items-center gap-2 shadow-sm hover:shadow-md">
                    <x-icon name="check-circle" size="sm" />
                    Report Found Item
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div
                    class="mb-6 bg-sacli-green-50 border border-sacli-green-200 text-sacli-green-700 px-4 py-3 rounded-xl flex items-center gap-2">
                    <x-icon name="check-circle" size="md" class="text-sacli-green-600 flex-shrink-0" />
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div
                    class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2">
                    <x-icon name="exclamation-circle" size="md" class="text-red-600 flex-shrink-0" />
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="p-6 text-gray-900">
                    @if ($items->count() > 0)
                        <!-- Items Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($items as $item)
                                <div class="relative">
                                    <!-- Status Badge Overlay -->
                                    <div class="absolute top-3 right-3 z-10">
                                        <x-status-badge :status="$item->status" />
                                    </div>

                                    <!-- Item Card -->
                                    <a href="{{ route('items.view', $item) }}" class="block">
                                        <x-item-card :item="$item" />
                                    </a>

                                    <!-- Admin Notes (if rejected) -->
                                    @if ($item->status === 'rejected' && $item->admin_notes)
                                        <div class="mt-3 bg-red-50 border border-red-200 rounded-lg p-3">
                                            <p class="text-xs font-medium text-red-800 mb-1 flex items-center gap-1">
                                                <x-icon name="exclamation-triangle" size="xs" />
                                                Admin Notes:
                                            </p>
                                            <p class="text-xs text-red-700">{{ $item->admin_notes }}</p>
                                        </div>
                                    @endif

                                    <!-- Reference Number -->
                                    <div
                                        class="mt-3 text-xs text-gray-500 font-mono bg-gray-50 px-3 py-2 rounded-lg flex items-center justify-between">
                                        <span class="flex items-center gap-1">
                                            <x-icon name="hashtag" size="xs" />
                                            Ref: {{ $item->id }}
                                        </span>
                                        <span class="text-gray-400">
                                            <x-duration-badge :item="$item" :showIcon="false" :showTooltip="false" />
                                        </span>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="mt-3 flex items-center justify-between gap-2">
                                        <a href="{{ route('items.view', $item) }}"
                                            class="flex-1 text-center text-sacli-green-600 hover:text-sacli-green-700 text-sm font-medium py-2 px-3 rounded-lg hover:bg-sacli-green-50 transition-colors inline-flex items-center justify-center gap-1">
                                            <x-icon name="eye" size="sm" />
                                            View Details
                                        </a>

                                        @if ($item->status === 'pending')
                                            <a href="{{ route('items.edit', $item) }}"
                                                class="text-blue-600 hover:text-blue-700 text-sm font-medium py-2 px-3 rounded-lg hover:bg-blue-50 transition-colors inline-flex items-center gap-1">
                                                <x-icon name="pencil-square" size="sm" />
                                                Edit
                                            </a>
                                            <form action="{{ route('items.destroy', $item) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Are you sure you want to delete this item?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-700 text-sm font-medium py-2 px-3 rounded-lg hover:bg-red-50 transition-colors inline-flex items-center gap-1">
                                                    <x-icon name="trash" size="sm" />
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
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
                        <x-empty-state icon="folder-open" title="No items yet"
                            message="Get started by reporting a lost or found item.">
                            <div class="flex flex-col sm:flex-row justify-center gap-3">
                                <a href="{{ route('items.create', ['type' => 'lost']) }}"
                                    class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                    <x-icon name="exclamation-circle" size="sm" />
                                    Report Lost Item
                                </a>
                                <a href="{{ route('items.create', ['type' => 'found']) }}"
                                    class="inline-flex items-center justify-center gap-2 bg-sacli-green-600 hover:bg-sacli-green-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                                    <x-icon name="check-circle" size="sm" />
                                    Report Found Item
                                </a>
                            </div>
                        </x-empty-state>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
