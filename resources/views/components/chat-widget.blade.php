@auth
    <!-- Floating Chat Support Widget -->
    <div class="fixed bottom-6 right-6 z-50">
        <a href="{{ route('chat.index') }}"
            class="group relative flex items-center justify-center w-14 h-14 bg-sacli-green-400 hover:bg-sacli-green-500 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 ease-in-out transform hover:scale-110"
            aria-label="Open chat support">

            <!-- Chat Icon -->
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                </path>
            </svg>

            <!-- Unread Count Badge -->
            <span id="chat-unread-badge"
                class="absolute -top-1 -right-1 hidden items-center justify-center min-w-[20px] h-5 px-1.5 bg-red-500 text-white text-xs font-bold rounded-full border-2 border-white"
                aria-live="polite" aria-atomic="true">
                0
            </span>

            <!-- Tooltip -->
            <span
                class="absolute bottom-full right-0 mb-2 px-3 py-1 bg-gray-900 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap pointer-events-none">
                Chat Support
            </span>
        </a>
    </div>

    <!-- Chat Widget Script -->
    <script>
        (function() {
            const badge = document.getElementById('chat-unread-badge');
            let pollInterval;

            // Function to update unread count
            async function updateUnreadCount() {
                try {
                    const response = await fetch('{{ route('chat.unread') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        throw new Error('Failed to fetch unread count');
                    }

                    const data = await response.json();

                    if (data.success && typeof data.unread_count === 'number') {
                        const count = data.unread_count;

                        if (count > 0) {
                            badge.textContent = count > 99 ? '99+' : count;
                            badge.classList.remove('hidden');
                            badge.classList.add('flex');
                        } else {
                            badge.classList.remove('flex');
                            badge.classList.add('hidden');
                        }
                    }
                } catch (error) {
                    console.error('Error fetching unread count:', error);
                    // Don't show error to user, just log it
                }
            }

            // Initial update
            updateUnreadCount();

            // Poll every 10 seconds for unread count
            pollInterval = setInterval(updateUnreadCount, 10000);

            // Clean up on page unload
            window.addEventListener('beforeunload', function() {
                if (pollInterval) {
                    clearInterval(pollInterval);
                }
            });

            // Handle Turbo navigation if present
            if (window.Turbo) {
                document.addEventListener('turbo:before-cache', function() {
                    if (pollInterval) {
                        clearInterval(pollInterval);
                    }
                });

                document.addEventListener('turbo:load', function() {
                    updateUnreadCount();
                    pollInterval = setInterval(updateUnreadCount, 10000);
                });
            }
        })
        ();
    </script>
@endauth
