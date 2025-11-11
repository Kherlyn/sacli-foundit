<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-sacli-green-800 leading-tight flex items-center gap-2">
                <x-icon name="squares-2x2" size="md" class="text-sacli-green-600" />
                {{ __('My Items') }}
            </h2>
            <x-primary-button :href="route('items.create')" icon="plus-circle" tag="a">
                {{ __('Submit Item') }}
            </x-primary-button>
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

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm border border-sacli-green-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <x-icon name="document-text" size="lg" class="text-blue-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Reports</p>
                            <p class="text-2xl font-bold text-gray-900">{{ Auth::user()->items()->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm border border-sacli-green-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <x-icon name="check-circle" size="lg" class="text-green-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Verified</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ Auth::user()->items()->where('status', 'verified')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm border border-sacli-green-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                            <x-icon name="clock" size="lg" class="text-yellow-600" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Pending</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ Auth::user()->items()->where('status', 'pending')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Section -->
            <div class="bg-white overflow-hidden shadow-sm border border-sacli-green-200 rounded-xl">
                <div class="p-6 text-gray-900">
                    @php
                        $items = Auth::user()->items()->latest()->paginate(12);
                    @endphp

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
                                            View
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
                        @if ($items->hasPages())
                            <div class="mt-8">
                                {{ $items->links() }}
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <div
                                class="w-20 h-20 bg-sacli-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <x-icon name="inbox" size="xl" class="text-sacli-green-600" />
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No items reported yet</h3>
                            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                                Haven't submitted any items yet? Help reunite people with their belongings by reporting
                                lost or found items.
                            </p>
                            <x-primary-button :href="route('items.create')" icon="plus-circle" tag="a"
                                class="text-lg px-8 py-3">
                                Submit Your First Item
                            </x-primary-button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
