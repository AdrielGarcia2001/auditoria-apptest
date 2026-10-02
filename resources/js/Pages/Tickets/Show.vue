<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    ticket: Object,
});

const statusLabels = {
    pending: 'Pendiente',
    assigned: 'Asignado',
    in_progress: 'En progreso',
    completed: 'Completado',
    verified: 'Verificado',
    reopened: 'Reabierto',
    cancelled: 'Cancelado',
};

const priorityColors = {
    low: 'bg-gray-100 text-gray-800',
    medium: 'bg-blue-100 text-blue-800',
    high: 'bg-orange-100 text-orange-800',
    critical: 'bg-red-100 text-red-800',
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
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ ticket.ticket_number }}</h1>
                    <p class="text-gray-600">{{ ticket.title }}</p>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('tickets.edit', ticket.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                    >
                        Editar
                    </Link>
                    <Link
                        :href="route('tickets.index')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                    >
                        Volver
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Info principal -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Descripción</h2>
                        <p class="text-gray-700">{{ ticket.description }}</p>
                    </div>

                    <!-- Visitas -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Visitas ({{ ticket.visits?.length || 0 }})</h2>
                        <div v-if="ticket.visits?.length" class="space-y-3">
                            <div
                                v-for="visit in ticket.visits"
                                :key="visit.id"
                                class="border border-gray-200 rounded-lg p-4"
                            >
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ visit.type === 'preventive' ? 'Preventiva' : 'Correctiva' }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Técnico: {{ visit.technician?.name || 'N/A' }}
                                        </p>
                                    </div>
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800': visit.status === 'scheduled',
                                            'bg-blue-100 text-blue-800': visit.status === 'in_route',
                                            'bg-purple-100 text-purple-800': visit.status === 'on_site',
                                            'bg-green-100 text-green-800': visit.status === 'completed',
                                            'bg-gray-100 text-gray-800': visit.status === 'cancelled',
                                        }"
                                    >
                                        {{ visit.status }}
                                    </span>
                                </div>
                                <div class="mt-2 text-sm text-gray-500">
                                    <p>Programada: {{ visit.scheduled_at ? new Date(visit.scheduled_at).toLocaleString() : 'N/A' }}</p>
                                    <p v-if="visit.started_at">Iniciada: {{ new Date(visit.started_at).toLocaleString() }}</p>
                                    <p v-if="visit.completed_at">Completada: {{ new Date(visit.completed_at).toLocaleString() }}</p>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-gray-500">No hay visitas registradas.</p>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Detalles</h2>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                <dd class="mt-1">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="statusColors[ticket.status]"
                                    >
                                        {{ statusLabels[ticket.status] }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Prioridad</dt>
                                <dd class="mt-1">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="priorityColors[ticket.priority]"
                                    >
                                        {{ ticket.priority }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Activo</dt>
                                <dd class="text-sm text-gray-900">{{ ticket.asset?.name || 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Creado por</dt>
                                <dd class="text-sm text-gray-900">{{ ticket.creator?.name || 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Asignado a</dt>
                                <dd class="text-sm text-gray-900">{{ ticket.assignee?.name || 'Sin asignar' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Cuadrilla</dt>
                                <dd class="text-sm text-gray-900">{{ ticket.crew?.name || 'Sin asignar' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha límite</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ ticket.due_date ? new Date(ticket.due_date).toLocaleDateString() : 'N/A' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
