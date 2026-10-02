<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    notifications: Object,
});

const formatDate = (date) => {
    return new Date(date).toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const typeColors = {
    info: 'bg-blue-100 text-blue-800',
    assignment: 'bg-purple-100 text-purple-800',
    warning: 'bg-yellow-100 text-yellow-800',
    success: 'bg-green-100 text-green-800',
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Notificaciones</h1>
                <button
                    @click="$inertia.post(route('notifications.read-all'))"
                    class="text-sm text-blue-600 hover:text-blue-800"
                >
                    Marcar todas como leídas
                </button>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div v-if="notifications.data.length === 0" class="p-8 text-center text-gray-500">
                    No hay notificaciones
                </div>
                <div
                    v-for="notification in notifications.data"
                    :key="notification.id"
                    class="p-4 border-b border-gray-100 hover:bg-gray-50"
                    :class="{ 'bg-blue-50': !notification.read_at }"
                >
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full"
                                    :class="typeColors[notification.data.type] || typeColors.info"
                                >
                                    {{ notification.data.type }}
                                </span>
                                <span v-if="!notification.read_at" class="text-xs text-blue-600 font-medium">
                                    Nueva
                                </span>
                            </div>
                            <p class="text-sm text-gray-900 mt-2">{{ notification.data.message }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ formatDate(notification.created_at) }}</p>
                        </div>
                        <div class="ml-4">
                            <Link
                                :href="notification.data.url"
                                class="text-blue-600 hover:text-blue-800 text-sm"
                            >
                                Ver
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paginación -->
            <div class="mt-4 flex justify-center">
                <div class="flex space-x-2">
                    <Link
                        v-for="link in notifications.links"
                        :key="link.label"
                        :href="link.url"
                        class="px-3 py-1 rounded border"
                        :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
