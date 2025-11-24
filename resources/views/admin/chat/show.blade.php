<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.chat.index') }}"
                    class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors duration-200">
                    <x-icon name="arrow-left" size="md" class="text-gray-600" />
                </a>
                <div
                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                    <x-icon name="user" size="md" class="text-white" />
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-sacli-green-800 leading-tight">
                        {{ $session->user->name }}
                    </h2>
                    <p class="text-sm text-gray-600">{{ $session->user->email }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                    @if ($session->status === 'open') bg-green-100 text-green-700 border border-green-200
                    @else bg-gray-100 text-gray-700 border border-gray-200 @endif">
                    {{ ucfirst($session->status) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 lg:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">
                <!-- Messages Container -->
                <div id="messages-container" class="p-6 space-y-4 max-h-[600px] overflow-y-auto">
                    @forelse ($messages as $message)
                        @if ($message->isFromUser())
                            <!-- User Message -->
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-gray-400 to-gray-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <x-icon name="user" size="sm" class="text-white" />
                                </div>
                                <div class="flex-1">
                                    <div
                                        class="bg-gray-100 rounded-2xl rounded-tl-sm px-4 py-3 inline-block max-w-[80%]">
                                        <p class="text-sm text-gray-900 whitespace-pre-wrap break-words">
                                            {{ $message->message }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1 ml-1">
                                        <span class="text-xs text-gray-500">
                                            {{ $message->created_at->format('M d, Y g:i A') }}
                                        </span>
                                        @if ($message->read_at)
                                            <span class="text-xs text-gray-400">• Read</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Admin Message -->
                            <div class="flex items-start gap-3 justify-end">
                                <div class="flex-1 flex justify-end">
                                    <div class="text-right">
                                        <div
                                            class="bg-gradient-to-br from-sacli-green-500 to-sacli-green-600 rounded-2xl rounded-tr-sm px-4 py-3 inline-block max-w-[80%]">
                                            <p class="text-sm text-white whitespace-pre-wrap break-words">
                                                {{ $message->message }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 mt-1 mr-1 justify-end">
                                            <span class="text-xs text-gray-500">
                                                {{ $message->created_at->format('M d, Y g:i A') }}
                                            </span>
                                            @if ($message->read_at)
                                                <span class="text-xs text-gray-400">• Read</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-sacli-green-500 to-sacli-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <x-icon name="shield-check" size="sm" class="text-white" />
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-12">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <x-icon name="chat-bubble-left-right" size="xl" class="text-gray-400" />
                            </div>
                            <p class="text-sm text-gray-500">No messages yet</p>
                            <p class="text-xs text-gray-400 mt-1">Start the conversation by sending a message below</p>
                        </div>
                    @endforelse
                </div>

                <!-- Message Input Form -->
                <div class="border-t border-gray-200 p-4 bg-gray-50">
                    <form id="message-form" class="flex gap-3">
                        @csrf
                        <div class="flex-1">
                            <textarea id="message-input" name="message" rows="2"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-sacli-green-500 focus:border-sacli-green-500 resize-none text-sm"
                                placeholder="Type your message..." required></textarea>
                            <div id="error-message" class="hidden mt-2 text-sm text-red-600"></div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <button type="submit" id="send-button"
                                class="px-6 py-3 bg-gradient-to-br from-sacli-green-500 to-sacli-green-600 hover:from-sacli-green-600 hover:to-sacli-green-700 text-white rounded-xl font-medium transition-all duration-200 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <x-icon name="paper-airplane" size="sm" />
                                <span>Send</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- User Information Card -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm rounded-xl">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <x-icon name="information-circle" size="md" class="text-sacli-green-600" />
                        User Information
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Name</p>
                            <p class="text-sm font-medium text-gray-900">{{ $session->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Email</p>
                            <p class="text-sm font-medium text-gray-900">{{ $session->user->email }}</p>
                        </div>
                        @if ($session->user->course)
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Course</p>
                                <p class="text-sm font-medium text-gray-900">{{ $session->user->course }}</p>
                            </div>
                        @endif
                        @if ($session->user->year)
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Year</p>
                                <p class="text-sm font-medium text-gray-900">{{ $session->user->year }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Member Since</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $session->user->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Session Started</p>
                            <p class="text-sm font-medium text-gray-900">{{ $session->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Chat functionality
            const sessionId = {{ $session->id }};
            const messagesContainer = document.getElementById('messages-container');
            const messageForm = document.getElementById('message-form');
            const messageInput = document.getElementById('message-input');
            const sendButton = document.getElementById('send-button');
            const errorMessage = document.getElementById('error-message');
            let lastMessageTime = '{{ $messages->last()?->created_at?->toIso8601String() ?? now()->toIso8601String() }}';
            let isPolling = false;
            let pollInterval = null;
            let pollDelay = 3000; // Start with 3 seconds
            let consecutiveErrors = 0;
            const MAX_POLL_DELAY = 30000; // Max 30 seconds
            const BASE_POLL_DELAY = 3000; // Base 3 seconds

            // Scroll to bottom of messages
            function scrollToBottom() {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            // Initial scroll
            scrollToBottom();

            // Send message
            messageForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const message = messageInput.value.trim();
                if (!message) return;

                // Disable form
                sendButton.disabled = true;
                messageInput.disabled = true;
                errorMessage.classList.add('hidden');

                try {
                    const response = await fetch(`/admin/chat/${sessionId}/messages`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            message: message
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Clear input
                        messageInput.value = '';

                        // Add message to UI
                        appendMessage(data.message, true);

                        // Update last message time
                        lastMessageTime = data.message.created_at;

                        // Scroll to bottom
                        scrollToBottom();

                        // Notify parent window if opened from session list
                        notifyParentWindow();
                    } else {
                        throw new Error(data.message || 'Failed to send message');
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                    errorMessage.textContent = error.message || 'Failed to send message. Please try again.';
                    errorMessage.classList.remove('hidden');
                } finally {
                    sendButton.disabled = false;
                    messageInput.disabled = false;
                    messageInput.focus();
                }
            });

            // Append message to UI
            function appendMessage(message, isAdmin) {
                const messageDiv = document.createElement('div');
                const timestamp = new Date(message.created_at);
                const formattedTime = timestamp.toLocaleString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });

                if (isAdmin) {
                    messageDiv.className = 'flex items-start gap-3 justify-end';
                    messageDiv.innerHTML = `
                        <div class="flex-1 flex justify-end">
                            <div class="text-right">
                                <div class="bg-gradient-to-br from-sacli-green-500 to-sacli-green-600 rounded-2xl rounded-tr-sm px-4 py-3 inline-block max-w-[80%]">
                                    <p class="text-sm text-white whitespace-pre-wrap break-words">${escapeHtml(message.message)}</p>
                                </div>
                                <div class="flex items-center gap-2 mt-1 mr-1 justify-end">
                                    <span class="text-xs text-gray-500">${formattedTime}</span>
                                    ${message.read_at ? '<span class="text-xs text-gray-400">• Read</span>' : ''}
                                </div>
                            </div>
                        </div>
                        <div class="w-8 h-8 bg-gradient-to-br from-sacli-green-500 to-sacli-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    `;
                } else {
                    messageDiv.className = 'flex items-start gap-3';
                    messageDiv.innerHTML = `
                        <div class="w-8 h-8 bg-gradient-to-br from-gray-400 to-gray-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="bg-gray-100 rounded-2xl rounded-tl-sm px-4 py-3 inline-block max-w-[80%]">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap break-words">${escapeHtml(message.message)}</p>
                            </div>
                            <div class="flex items-center gap-2 mt-1 ml-1">
                                <span class="text-xs text-gray-500">${formattedTime}</span>
                                ${message.read_at ? '<span class="text-xs text-gray-400">• Read</span>' : ''}
                            </div>
                        </div>
                    `;
                }

                messagesContainer.appendChild(messageDiv);
            }

            // Escape HTML to prevent XSS
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Notify parent window of activity (if opened from session list)
            function notifyParentWindow() {
                try {
                    if (window.opener && !window.opener.closed) {
                        window.opener.postMessage({
                            type: 'chat_activity',
                            sessionId: sessionId
                        }, window.location.origin);
                    }
                } catch (e) {
                    // Ignore cross-origin errors
                    console.debug('Could not notify parent window:', e);
                }
            }

            // Mark messages as read when viewing
            async function markMessagesAsRead() {
                try {
                    // The controller already marks messages as read on page load
                    // This function can be called after polling to mark new user messages as read
                    const response = await fetch(`/admin/chat/${sessionId}/messages`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        // Messages are marked as read by the controller when fetched
                        console.debug('Messages marked as read');
                    }
                } catch (error) {
                    console.debug('Error marking messages as read:', error);
                }
            }

            // Poll for new messages with exponential backoff
            async function pollMessages() {
                if (isPolling) return;
                isPolling = true;

                try {
                    const response = await fetch(
                        `/admin/chat/${sessionId}/messages?since=${encodeURIComponent(lastMessageTime)}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success && data.messages.length > 0) {
                        let hasNewUserMessages = false;

                        data.messages.forEach(message => {
                            appendMessage(message, message.sender_type === 'admin');
                            lastMessageTime = message.created_at;

                            // Check if there are new user messages
                            if (message.sender_type === 'user') {
                                hasNewUserMessages = true;
                            }
                        });

                        scrollToBottom();

                        // Mark new user messages as read since admin is viewing them
                        if (hasNewUserMessages) {
                            markMessagesAsRead();
                        }

                        // Notify parent window of new activity
                        notifyParentWindow();
                    }

                    // Reset error count and poll delay on success
                    consecutiveErrors = 0;
                    if (pollDelay !== BASE_POLL_DELAY) {
                        pollDelay = BASE_POLL_DELAY;
                        restartPolling();
                    }
                } catch (error) {
                    console.error('Error polling messages:', error);
                    consecutiveErrors++;

                    // Implement exponential backoff
                    const newDelay = Math.min(
                        BASE_POLL_DELAY * Math.pow(2, consecutiveErrors),
                        MAX_POLL_DELAY
                    );

                    if (newDelay !== pollDelay) {
                        pollDelay = newDelay;
                        console.log(`Polling error. Retrying in ${pollDelay / 1000} seconds...`);
                        restartPolling();
                    }
                } finally {
                    isPolling = false;
                }
            }

            // Restart polling with new delay
            function restartPolling() {
                if (pollInterval) {
                    clearInterval(pollInterval);
                }
                pollInterval = setInterval(pollMessages, pollDelay);
            }

            // Start polling every 3 seconds initially
            pollInterval = setInterval(pollMessages, pollDelay);

            // Reset polling on visibility change (when tab becomes active)
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    // Reset error count when user returns to tab
                    consecutiveErrors = 0;
                    pollDelay = BASE_POLL_DELAY;
                    restartPolling();
                    // Immediately poll for new messages
                    pollMessages();
                }
            });

            // Clean up on page unload
            window.addEventListener('beforeunload', () => {
                if (pollInterval) {
                    clearInterval(pollInterval);
                }
            });

            // Auto-resize textarea
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 150) + 'px';
            });

            // Submit on Ctrl/Cmd + Enter
            messageInput.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    messageForm.dispatchEvent(new Event('submit'));
                }
            });
        </script>
    @endpush
</x-admin-layout>
