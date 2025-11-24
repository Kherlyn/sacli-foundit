<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                    <x-icon name="chat-bubble-left-right" size="md" class="text-white" />
                </div>
                <h2 class="font-semibold text-xl text-sacli-green-800 leading-tight">
                    {{ __('Chat Support') }}
                </h2>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <x-icon name="users" size="sm" />
                <span>{{ $sessions->count() }} {{ Str::plural('conversation', $sessions->count()) }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 lg:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="p-6">
                    @if ($sessions->count() > 0)
                        <div class="space-y-3">
                            @foreach ($sessions as $session)
                                <a href="{{ route('admin.chat.show', $session) }}"
                                    class="block p-4 border border-gray-200 rounded-xl hover:border-sacli-green-300 hover:bg-sacli-green-50 transition-all duration-200 hover:shadow-md">
                                    <div class="flex items-start justify-between gap-4">
                                        <!-- User Info -->
                                        <div class="flex items-start gap-3 flex-1 min-w-0">
                                            <div
                                                class="w-12 h-12 bg-gradient-to-br from-sacli-green-500 to-sacli-green-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                                <x-icon name="user" size="md" class="text-white" />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h3 class="text-base font-semibold text-gray-900 truncate">
                                                        {{ $session->user->name }}
                                                    </h3>
                                                    @if ($session->unread_user_messages_count > 0)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                                            {{ $session->unread_user_messages_count }} new
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-600 truncate">
                                                    {{ $session->user->email }}
                                                </p>
                                                @if ($session->last_message_at)
                                                    <div class="flex items-center gap-1 mt-2 text-xs text-gray-500">
                                                        <x-icon name="clock" size="xs" />
                                                        <span>Last message:
                                                            {{ $session->last_message_at->diffForHumans() }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Status & Action -->
                                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                @if ($session->status === 'open') bg-green-100 text-green-700 border border-green-200
                                                @else bg-gray-100 text-gray-700 border border-gray-200 @endif">
                                                {{ ucfirst($session->status) }}
                                            </span>
                                            <div
                                                class="flex items-center gap-1 text-sacli-green-600 text-sm font-medium">
                                                <span>View</span>
                                                <x-icon name="arrow-right" size="sm" />
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <x-empty-state icon="chat-bubble-left-right" title="No chat sessions yet"
                            message="When users start conversations, they will appear here." />
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Listen for activity updates from child windows
            window.addEventListener('message', (event) => {
                // Verify origin for security
                if (event.origin !== window.location.origin) {
                    return;
                }

                // Handle chat activity updates
                if (event.data && event.data.type === 'chat_activity') {
                    console.log('Chat activity detected for session:', event.data.sessionId);
                    // Reload the page to show updated activity
                    // In a production app, you might want to use AJAX to update just the affected session
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            });
        </script>
    @endpush
</x-admin-layout>
