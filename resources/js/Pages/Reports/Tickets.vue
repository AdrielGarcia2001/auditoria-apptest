<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    tickets: Array,
    filters: Object,
    statuses: Array,
});

const form = useForm({
    status: props.filters.status || '',
    priority: props.filters.priority || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
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

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800',
    assigned: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-purple-100 text-purple-800',
    completed: 'bg-green-100 text-green-800',
    verified: 'bg-emerald-100 text-emerald-800',
    reopened: 'bg-orange-100 text-orange-800',
    cancelled: 'bg-gray-100 text-gray-800',
};

const priorityColors = {
    low: 'bg-gray-100 text-gray-800',
    medium: 'bg-blue-100 text-blue-800',
    high: 'bg-orange-100 text-orange-800',
    critical: 'bg-red-100 text-red-800',
};

const submit = () => {
    form.get(route('reports.tickets'));
};

const exportPdf = () => {
    const params = new URLSearchParams();
    if (form.status) params.append('status', form.status);
    if (form.priority) params.append('priority', form.priority);
    if (form.date_from) params.append('date_from', form.date_from);
    if (form.date_to) params.append('date_to', form.date_to);
    window.location.href = route('reports.tickets-pdf') + '?' + params.toString();
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Reporte de Tickets</h1>
                <button
                    @click="exportPdf"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm"
                >
                    Exportar PDF
                </button>
            </div>

            <!-- Filtros -->
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select v-model="form.status" class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">Todos</option>
                            <option v-for="status in statuses" :key="status.value" :value="status.value">
                                {{ status.label }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
                        <select v-model="form.priority" class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">Todas</option>
                            <option value="low">Baja</option>
                            <option value="medium">Media</option>
                            <option value="high">Alta</option>
                            <option value="critical">Crítica</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                        <input v-model="form.date_from" type="date" class="w-full border-gray-300 rounded-lg shadow-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                        <input v-model="form.date_to" type="date" class="w-full border-gray-300 rounded-lg shadow-sm" />
                    </div>
                    <div class="md:col-span-4">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioridad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asignado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha límite</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="ticket in tickets" :key="ticket.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ ticket.ticket_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ticket.title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="statusColors[ticket.status] || 'bg-gray-100 text-gray-800'"
                                >
                                    {{ statusLabels[ticket.status] || ticket.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="priorityColors[ticket.priority] || 'bg-gray-100 text-gray-800'"
                                >
                                    {{ ticket.priority }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ticket.asset?.name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ticket.assignee?.name || 'Sin asignar' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ticket.due_date ? new Date(ticket.due_date).toLocaleDateString() : 'N/A' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-4 text-sm text-gray-500">Total: {{ tickets.length }} tickets</p>
        </div>
    </AuthenticatedLayout>
</template>
