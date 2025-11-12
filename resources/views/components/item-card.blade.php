@props(['item'])

<div
    class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:border-sacli-green-400 hover:-translate-y-1 transition-all duration-300 ease-in-out group flex flex-col h-full">
    <!-- Image Section -->
    <div
        class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center relative overflow-hidden flex-shrink-0">
        @if ($item->images && $item->images->count() > 0)
            <img src="{{ $item->images->first()->url }}" alt="{{ $item->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <x-icon name="photo" size="xl" class="text-gray-400" />
        @endif

        <!-- Type Badge -->
        <div class="absolute top-3 left-3">
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm backdrop-blur-sm {{ $item->type === 'found' ? 'bg-sacli-green-100/90 text-sacli-green-800' : 'bg-red-100/90 text-red-800' }}">
                <x-icon :name="$item->type === 'found' ? 'check-circle' : 'exclamation-circle'" size="xs" />
                {{ ucfirst($item->type) }}
            </span>
        </div>
    </div>

    <!-- Content Section -->
    <div class="p-5 flex flex-col flex-grow">
        <div class="flex items-start justify-between mb-3">
            <h3
                class="font-semibold text-gray-900 text-lg line-clamp-1 group-hover:text-sacli-green-600 transition-colors duration-200 flex-grow">
                {{ $item->title }}
            </h3>
            <x-icon name="arrow-right" size="sm"
                class="text-gray-400 group-hover:text-sacli-green-600 group-hover:translate-x-1 transition-all duration-200 flex-shrink-0 ml-2" />
        </div>

        <p class="text-gray-600 text-sm mb-4 line-clamp-2 min-h-[2.5rem]">
            {{ $item->description }}
        </p>

        <!-- Metadata -->
        <div class="space-y-2 mt-auto">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <x-icon name="map-pin" size="sm" class="text-gray-400 flex-shrink-0" />
                <span class="line-clamp-1">{{ $item->location }}</span>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <x-icon name="calendar" size="sm" class="text-gray-400 flex-shrink-0" />
                <span>{{ $item->date_occurred->format('M d, Y') }}</span>
            </div>
            <div class="flex items-center gap-2 text-sm">
                <x-duration-badge :item="$item" />
            </div>
            @if ($item->user && ($item->user->course || $item->user->year))
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <x-icon name="academic-cap" size="xs" class="text-gray-400 flex-shrink-0" />
                    <span class="line-clamp-1">
                        @if ($item->user->course)
                            {{ $item->user->course }}
                        @else
                            <span class="text-gray-400">Course not specified</span>
                        @endif
                        @if ($item->user->year)
                            <span class="text-gray-400 mx-1">•</span>
                            Year {{ $item->user->year }}
                        @endif
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>
