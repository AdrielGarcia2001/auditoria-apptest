<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    ticketsByPriority: Object,
    ticketsByStatus: Object,
    recentTickets: Array,
    upcomingVisits: Array,
});

const priorityColors = {
    low: 'bg-gray-100 text-gray-800',
    medium: 'bg-blue-100 text-blue-800',
    high: 'bg-orange-100 text-orange-800',
    critical: 'bg-red-100 text-red-800',
};

const statusLabels = {
    pending: 'Pendiente',
    assigned: 'Asignado',
    in_progress: 'En progreso',
    completed: 'Completado',
    verified: 'Verificado',
    reopened: 'Reabierto',
    cancelled: 'Cancelado',
};

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800',
    assigned: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-purple-100 text-purple-800',
    completed: 'bg-green-100 text-green-800',
    verified: 'bg-emerald-100 text-emerald-800',
    reopened: 'bg-orange-100 text-orange-800',
    cancelled: 'bg-gray-100 text-gray-800',
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Total Tickets</p>
                    <p class="text-3xl font-bold text-gray-900">{{ stats.totalTickets }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Pendientes</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ stats.pendingTickets }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-500">En Progreso</p>
                    <p class="text-3xl font-bold text-purple-600">{{ stats.inProgressTickets }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Completados</p>
                    <p class="text-3xl font-bold text-green-600">{{ stats.completedTickets }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Total Visitas</p>
                    <p class="text-3xl font-bold text-gray-900">{{ stats.totalVisits }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Programadas</p>
                    <p class="text-3xl font-bold text-blue-600">{{ stats.scheduledVisits }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Completadas</p>
                    <p class="text-3xl font-bold text-green-600">{{ stats.completedVisits }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <p class="text-sm text-gray-500">Tickets Vencidos</p>
                    <p class="text-3xl font-bold text-red-600">{{ stats.overdueTickets }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Tickets por Estado -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Tickets por Estado</h2>
                    <div class="space-y-3">
                        <div v-for="(count, status) in ticketsByStatus" :key="status" class="flex items-center justify-between">
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full"
                                :class="statusColors[status] || 'bg-gray-100 text-gray-800'"
                            >
                                {{ statusLabels[status] || status }}
                            </span>
                            <span class="text-sm font-medium text-gray-900">{{ count }}</span>
                        </div>
                    </div>
                </div>

                <!-- Tickets por Prioridad -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Tickets por Prioridad</h2>
                    <div class="space-y-3">
                        <div v-for="(count, priority) in ticketsByPriority" :key="priority" class="flex items-center justify-between">
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full"
                                :class="priorityColors[priority] || 'bg-gray-100 text-gray-800'"
                            >
                                {{ priority }}
                            </span>
                            <span class="text-sm font-medium text-gray-900">{{ count }}</span>
                        </div>
                    </div>
                </div>

                <!-- Tickets Recientes -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Tickets Recientes</h2>
                    <div class="space-y-3">
                        <div v-for="ticket in recentTickets" :key="ticket.id" class="border-b border-gray-100 pb-3 last:border-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ ticket.ticket_number }}</p>
                                    <p class="text-xs text-gray-500">{{ ticket.title }}</p>
                                </div>
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full"
                                    :class="statusColors[ticket.status] || 'bg-gray-100 text-gray-800'"
                                >
                                    {{ statusLabels[ticket.status] || ticket.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <Link :href="route('tickets.index')" class="mt-4 inline-block text-sm text-blue-600 hover:text-blue-800">
                        Ver todos →
                    </Link>
                </div>

                <!-- Próximas Visitas -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Próximas Visitas</h2>
                    <div class="space-y-3">
                        <div v-for="visit in upcomingVisits" :key="visit.id" class="border-b border-gray-100 pb-3 last:border-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ visit.ticket?.ticket_number }}</p>
                                    <p class="text-xs text-gray-500">{{ visit.technician?.name || 'Sin asignar' }}</p>
                                </div>
                                <span class="text-xs text-gray-500">{{ formatDate(visit.scheduled_at) }}</span>
                            </div>
                        </div>
                    </div>
                    <Link :href="route('visits.index')" class="mt-4 inline-block text-sm text-blue-600 hover:text-blue-800">
                        Ver todas →
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
