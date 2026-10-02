<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const notifications = ref([]);
const count = ref(0);
const isOpen = ref(false);
let pollInterval = null;

const csrfToken = () => {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]*)/);
    return match ? decodeURIComponent(match[1]) : '';
};

const jsonHeaders = () => ({
    Accept: 'application/json',
    'X-XSRF-TOKEN': csrfToken(),
});

const fetchNotifications = async () => {
    try {
        const response = await fetch(route('notifications.unread'), {
            headers: jsonHeaders(),
            credentials: 'same-origin',
        });

        if (!response.ok) {
            return;
        }

        const data = await response.json();
        notifications.value = data.notifications ?? [];
        count.value = data.count ?? 0;
    } catch (error) {
        console.error('Error fetching notifications:', error);
    }
};

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        fetchNotifications();
    }
};

const markAsRead = async (id) => {
    try {
        await fetch(route('notifications.read', id), {
            method: 'POST',
            headers: jsonHeaders(),
            credentials: 'same-origin',
        });
        notifications.value = notifications.value.filter(n => n.id !== id);
        count.value = Math.max(0, count.value - 1);
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
};

const markAllAsRead = async () => {
    try {
        await fetch(route('notifications.read-all'), {
            method: 'POST',
            headers: jsonHeaders(),
            credentials: 'same-origin',
        });
        notifications.value = [];
        count.value = 0;
    } catch (error) {
        console.error('Error marking all notifications as read:', error);
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

onMounted(() => {
    fetchNotifications();
    pollInterval = setInterval(fetchNotifications, 30000);
});

onUnmounted(() => {
    if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
});
</script>

<template>
    <div class="relative">
        <button
            @click="toggleDropdown"
            class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none"
        >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span
                v-if="count > 0"
                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full"
            >
                {{ count > 99 ? '99+' : count }}
            </span>
        </button>

        <div
            v-if="isOpen"
            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
        >
            <div class="p-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-900">Notificaciones</h3>
                    <button
                        v-if="count > 0"
                        @click="markAllAsRead"
                        class="text-xs text-blue-600 hover:text-blue-800"
                    >
                        Marcar todas como leídas
                    </button>
                </div>
            </div>

            <div class="max-h-96 overflow-y-auto">
                <div v-if="notifications.length === 0" class="p-4 text-center text-gray-500 text-sm">
                    No hay notificaciones
                </div>
                <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer"
                    @click="markAsRead(notification.id)"
                >
                    <Link :href="notification.data.url" class="block">
                        <p class="text-sm text-gray-900">{{ notification.data.message }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ formatDate(notification.created_at) }}</p>
                    </Link>
                </div>
            </div>

            <div class="p-3 border-t border-gray-200 text-center">
                <Link
                    :href="route('notifications.index')"
                    class="text-sm text-blue-600 hover:text-blue-800"
                >
                    Ver todas las notificaciones
                </Link>
            </div>
        </div>
    </div>
</template>
