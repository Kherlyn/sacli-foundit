<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-sacli-green-800 leading-tight flex items-center gap-2">
                <x-icon name="chat-bubble-left-right" size="md" class="text-sacli-green-600" />
                {{ __('Support Chat') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm border border-sacli-green-200 rounded-xl">
                <!-- Chat Header -->
                <div class="bg-sacli-green-50 border-b border-sacli-green-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-sacli-green-800 flex items-center gap-2">
                                <x-icon name="user-circle" size="md" class="text-sacli-green-600" />
                                Chat with Support
                            </h3>
                            <p class="text-sm text-sacli-green-600 mt-1">We're here to help you</p>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-sacli-green-600">
                            <span class="w-2 h-2 bg-sacli-green-500 rounded-full animate-pulse"></span>
                            <span>Online</span>
                        </div>
                    </div>
                </div>

                <!-- Messages Container -->
                <div id="chat-messages" class="p-6 space-y-4 overflow-y-auto bg-gray-50" style="height: 500px;">
                    @forelse ($messages as $message)
                        @if ($message->sender_type === 'user')
                            <!-- User Message -->
                            <div class="flex justify-end"
                                data-message-timestamp="{{ $message->created_at->toIso8601String() }}">
                                <div class="max-w-[75%]">
                                    <div
                                        class="bg-sacli-green-600 text-white rounded-2xl rounded-tr-sm px-4 py-3 shadow-sm">
                                        <p class="text-sm break-words">{{ $message->message }}</p>
                                    </div>
                                    <div class="flex items-center justify-end gap-2 mt-1 px-2">
                                        <span class="text-xs text-gray-500">
                                            {{ $message->created_at->format('g:i A') }}
                                        </span>
                                        @if ($message->read_at)
                                            <x-icon name="check-circle" size="xs" class="text-sacli-green-600" />
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Admin Message -->
                            <div class="flex justify-start"
                                data-message-timestamp="{{ $message->created_at->toIso8601String() }}">
                                <div class="max-w-[75%]">
                                    <div class="flex items-start gap-2">
                                        <div
                                            class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                            <x-icon name="user" size="sm" class="text-blue-600" />
                                        </div>
                                        <div>
                                            <div
                                                class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm">
                                                <p class="text-sm text-gray-800 break-words">{{ $message->message }}</p>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1 px-2">
                                                <span class="text-xs font-medium text-blue-600">Support</span>
                                                <span class="text-xs text-gray-500">
                                                    {{ $message->created_at->format('g:i A') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <!-- Empty State -->
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 bg-sacli-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <x-icon name="chat-bubble-left-right" size="xl" class="text-sacli-green-600" />
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Start a conversation</h3>
                                <p class="text-gray-600 max-w-sm mx-auto">
                                    Send us a message and we'll get back to you as soon as possible.
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Message Input Form -->
                <div class="border-t border-gray-200 bg-white px-6 py-4">
                    <form id="chat-form" class="flex items-end gap-3">
                        @csrf
                        <div class="flex-1">
                            <label for="message-input" class="sr-only">Type your message</label>
                            <textarea id="message-input" name="message" rows="1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-sacli-green-500 focus:border-sacli-green-500 resize-none transition-all duration-200"
                                placeholder="Type your message..." maxlength="5000" required></textarea>
                            <p class="text-xs text-gray-500 mt-1 px-1">
                                <span id="char-count">0</span> / 5000 characters
                            </p>
                        </div>
                        <button type="submit" id="send-button"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-sacli-green-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-sacli-green-700 focus:bg-sacli-green-700 active:bg-sacli-green-800 active:scale-95 focus:outline-none focus:ring-2 focus:ring-sacli-green-500 focus:ring-offset-2 transition-all duration-200 ease-in-out shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                            aria-label="Send message">
                            <span id="send-button-text">Send</span>
                            <x-icon name="paper-airplane" size="sm" id="send-icon" />
                            <div id="send-spinner" class="hidden">
                                <svg class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Chat functionality
            (function() {
                const chatMessages = document.getElementById('chat-messages');
                const chatForm = document.getElementById('chat-form');
                const messageInput = document.getElementById('message-input');
                const sendButton = document.getElementById('send-button');
                const sendButtonText = document.getElementById('send-button-text');
                const sendIcon = document.getElementById('send-icon');
                const sendSpinner = document.getElementById('send-spinner');
                const charCount = document.getElementById('char-count');

                let isSubmitting = false;
                let lastMessageTimestamp = null;
                let pollingInterval = null;
                let pollingDelay = 3000; // Start with 3 seconds
                let consecutiveErrors = 0;
                const MAX_POLLING_DELAY = 30000; // Max 30 seconds
                const MIN_POLLING_DELAY = 3000; // Min 3 seconds

                // Initialize last message timestamp from existing messages
                const existingMessages = chatMessages.querySelectorAll('[data-message-timestamp]');
                if (existingMessages.length > 0) {
                    const timestamps = Array.from(existingMessages).map(el => el.dataset.messageTimestamp);
                    lastMessageTimestamp = timestamps[timestamps.length - 1];
                }

                // Auto-resize textarea
                messageInput.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 150) + 'px';

                    // Update character count
                    charCount.textContent = this.value.length;
                });

                // Scroll to bottom of messages
                function scrollToBottom() {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }

                // Initial scroll to bottom
                scrollToBottom();

                // Show loading state
                function setLoadingState(loading) {
                    isSubmitting = loading;
                    sendButton.disabled = loading;

                    if (loading) {
                        sendButtonText.textContent = 'Sending...';
                        sendIcon.classList.add('hidden');
                        sendSpinner.classList.remove('hidden');
                    } else {
                        sendButtonText.textContent = 'Send';
                        sendIcon.classList.remove('hidden');
                        sendSpinner.classList.add('hidden');
                    }
                }

                // Create message element
                function createMessageElement(message, isUser) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = isUser ? 'flex justify-end' : 'flex justify-start';
                    messageDiv.dataset.messageTimestamp = message.created_at;

                    const timestamp = new Date(message.created_at);
                    const timeString = timestamp.toLocaleTimeString('en-US', {
                        hour: 'numeric',
                        minute: '2-digit'
                    });

                    if (isUser) {
                        messageDiv.innerHTML = `
                        <div class="max-w-[75%]">
                            <div class="bg-sacli-green-600 text-white rounded-2xl rounded-tr-sm px-4 py-3 shadow-sm">
                                <p class="text-sm break-words">${escapeHtml(message.message)}</p>
                            </div>
                            <div class="flex items-center justify-end gap-2 mt-1 px-2">
                                <span class="text-xs text-gray-500">${timeString}</span>
                            </div>
                        </div>
                    `;
                    } else {
                        messageDiv.innerHTML = `
                        <div class="max-w-[75%]">
                            <div class="flex items-start gap-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <iconify-icon icon="heroicons:user" width="16" height="16" class="text-blue-600"></iconify-icon>
                                </div>
                                <div>
                                    <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm">
                                        <p class="text-sm text-gray-800 break-words">${escapeHtml(message.message)}</p>
                                    </div>
                                    <div class="flex items-center gap-2 mt-1 px-2">
                                        <span class="text-xs font-medium text-blue-600">Support</span>
                                        <span class="text-xs text-gray-500">${timeString}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    }

                    return messageDiv;
                }

                // Escape HTML to prevent XSS
                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                // Remove empty state if it exists
                function removeEmptyState() {
                    const emptyState = chatMessages.querySelector('.text-center');
                    if (emptyState && emptyState.textContent.includes('Start a conversation')) {
                        emptyState.remove();
                    }
                }

                // Update unread count badge in navigation
                function updateUnreadCountBadge() {
                    fetch('{{ route('chat.unread') }}', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Dispatch custom event for other components to listen to
                                window.dispatchEvent(new CustomEvent('chat-unread-count-updated', {
                                    detail: {
                                        count: data.unread_count
                                    }
                                }));
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching unread count:', error);
                        });
                }

                // Handle form submission
                chatForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    if (isSubmitting) return;

                    const message = messageInput.value.trim();
                    if (!message) return;

                    setLoadingState(true);

                    try {
                        const response = await fetch('{{ route('chat.send') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                message
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Remove empty state
                            removeEmptyState();

                            // Add message to chat
                            const messageElement = createMessageElement(data.message, true);
                            chatMessages.appendChild(messageElement);

                            // Update last message timestamp
                            lastMessageTimestamp = data.message.created_at;

                            // Clear input
                            messageInput.value = '';
                            messageInput.style.height = 'auto';
                            charCount.textContent = '0';

                            // Scroll to bottom
                            scrollToBottom();
                        } else {
                            alert('Failed to send message. Please try again.');
                        }
                    } catch (error) {
                        console.error('Error sending message:', error);
                        alert('Failed to send message. Please check your connection and try again.');
                    } finally {
                        setLoadingState(false);
                        messageInput.focus();
                    }
                });

                // Poll for new messages with exponential backoff on errors
                async function pollMessages() {
                    if (!lastMessageTimestamp) return;

                    try {
                        const response = await fetch(
                            `{{ route('chat.messages') }}?since=${encodeURIComponent(lastMessageTimestamp)}`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        // Reset error count and polling delay on success
                        consecutiveErrors = 0;
                        if (pollingDelay !== MIN_POLLING_DELAY) {
                            pollingDelay = MIN_POLLING_DELAY;
                            restartPolling();
                        }

                        if (data.success && data.messages.length > 0) {
                            // Remove empty state if present
                            removeEmptyState();

                            // Track if we received any admin messages
                            let hasNewAdminMessages = false;

                            // Add new messages
                            data.messages.forEach(message => {
                                const isUser = message.sender_type === 'user';
                                if (!isUser) {
                                    hasNewAdminMessages = true;
                                }
                                const messageElement = createMessageElement(message, isUser);
                                chatMessages.appendChild(messageElement);

                                // Update last message timestamp
                                lastMessageTimestamp = message.created_at;
                            });

                            // Scroll to bottom
                            scrollToBottom();

                            // Update unread count badge if we received admin messages
                            if (hasNewAdminMessages) {
                                updateUnreadCountBadge();
                            }
                        }
                    } catch (error) {
                        console.error('Error polling messages:', error);

                        // Increment error count and apply exponential backoff
                        consecutiveErrors++;
                        const newDelay = Math.min(
                            MIN_POLLING_DELAY * Math.pow(2, consecutiveErrors),
                            MAX_POLLING_DELAY
                        );

                        if (newDelay !== pollingDelay) {
                            pollingDelay = newDelay;
                            console.log(`Polling error. Backing off to ${pollingDelay / 1000}s interval.`);
                            restartPolling();
                        }
                    }
                }

                // Restart polling with current delay
                function restartPolling() {
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                    }
                    pollingInterval = setInterval(pollMessages, pollingDelay);
                }

                // Start polling
                restartPolling();

                // Clean up on page unload
                window.addEventListener('beforeunload', function() {
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                    }
                });

                // Focus input on load
                messageInput.focus();
            })();
        </script>
    @endpush
</x-app-layout>
