<div x-data="notificationBell()" x-init="init()" class="relative">
    <!-- Notification Bell Button -->
    <button @click="toggleDropdown()" type="button"
        class="relative p-2 text-gray-600 hover:text-sacli-green-600 hover:bg-gray-100 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-sacli-green-500">
        <x-icon name="bell" size="md" />

        <!-- Unread Badge -->
        <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount"
            class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full min-w-[18px]">
        </span>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="isOpen" @click.away="isOpen = false" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-lg border border-gray-200 z-50 max-h-[32rem] overflow-hidden"
        style="display: none;">

        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
            <button @click="markAllAsRead()" x-show="unreadCount > 0" type="button"
                class="text-xs text-sacli-green-600 hover:text-sacli-green-700 font-medium">
                Mark all as read
            </button>
        </div>

        <!-- Notifications List -->
        <div class="overflow-y-auto max-h-96">
            <template x-if="notifications.length === 0">
                <div class="px-4 py-8 text-center">
                    <x-icon name="bell-slash" size="lg" class="mx-auto text-gray-400 mb-2" />
                    <p class="text-sm text-gray-500">No notifications yet</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div @click="markAsRead(notification.id)" :class="notification.read_at ? 'bg-white' : 'bg-blue-50'"
                    class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors duration-150">
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0 mt-1">
                            <template x-if="notification.data.status === 'verified'">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <x-icon name="check-circle" size="sm" class="text-green-600" />
                                </div>
                            </template>
                            <template x-if="notification.data.status === 'rejected'">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <x-icon name="x-circle" size="sm" class="text-red-600" />
                                </div>
                            </template>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900" x-text="notification.data.message"></p>
                            <p class="text-xs text-gray-500 mt-1" x-text="formatTime(notification.created_at)"></p>
                        </div>

                        <!-- Unread Indicator -->
                        <div x-show="!notification.read_at" class="flex-shrink-0">
                            <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div x-show="notifications.length > 0" class="px-4 py-2 border-t border-gray-200 bg-gray-50 text-center">
            <a href="{{ route('dashboard') }}"
                class="text-xs text-sacli-green-600 hover:text-sacli-green-700 font-medium">
                View all notifications
            </a>
        </div>
    </div>
</div>

<script>
    function notificationBell() {
        return {
            isOpen: false,
            notifications: [],
            unreadCount: 0,
            pollInterval: null,

            init() {
                this.fetchNotifications();
                // Poll for new notifications every 30 seconds
                this.pollInterval = setInterval(() => {
                    this.fetchNotifications();
                }, 30000);
            },

            toggleDropdown() {
                this.isOpen = !this.isOpen;
                if (this.isOpen) {
                    this.fetchNotifications();
                }
            },

            async fetchNotifications() {
                try {
                    const response = await fetch('{{ route('notifications.index') }}');
                    const data = await response.json();
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                } catch (error) {
                    console.error('Failed to fetch notifications:', error);
                }
            },

            async markAsRead(notificationId) {
                try {
                    await fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    // Update local state
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification && !notification.read_at) {
                        notification.read_at = new Date().toISOString();
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }

                    // Redirect to item if available
                    if (notification && notification.data.item_id) {
                        window.location.href = `/items/${notification.data.item_id}`;
                    }
                } catch (error) {
                    console.error('Failed to mark notification as read:', error);
                }
            },

            async markAllAsRead() {
                try {
                    await fetch('{{ route('notifications.read-all') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    // Update local state
                    this.notifications.forEach(n => {
                        n.read_at = new Date().toISOString();
                    });
                    this.unreadCount = 0;
                } catch (error) {
                    console.error('Failed to mark all as read:', error);
                }
            },

            formatTime(timestamp) {
                const date = new Date(timestamp);
                const now = new Date();
                const diff = Math.floor((now - date) / 1000); // seconds

                if (diff < 60) return 'Just now';
                if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
                if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
                if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`;

                return date.toLocaleDateString();
            }
        }
    }
</script>
