<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    visits: Array,
    filters: Object,
});

const form = useForm({
    status: props.filters.status || '',
    type: props.filters.type || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

const statusLabels = {
    scheduled: 'Programada',
    in_route: 'En ruta',
    on_site: 'En sitio',
    completed: 'Completada',
    cancelled: 'Cancelada',
};

const statusColors = {
    scheduled: 'bg-yellow-100 text-yellow-800',
    in_route: 'bg-blue-100 text-blue-800',
    on_site: 'bg-purple-100 text-purple-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-gray-100 text-gray-800',
};

const submit = () => {
    form.get(route('reports.visits'));
};

const exportPdf = () => {
    const params = new URLSearchParams();
    if (form.status) params.append('status', form.status);
    if (form.type) params.append('type', form.type);
    if (form.date_from) params.append('date_from', form.date_from);
    if (form.date_to) params.append('date_to', form.date_to);
    window.location.href = route('reports.visits-pdf') + '?' + params.toString();
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Reporte de Visitas</h1>
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
                            <option value="scheduled">Programada</option>
                            <option value="in_route">En ruta</option>
                            <option value="on_site">En sitio</option>
                            <option value="completed">Completada</option>
                            <option value="cancelled">Cancelada</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        <select v-model="form.type" class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">Todos</option>
                            <option value="preventive">Preventiva</option>
                            <option value="corrective">Correctiva</option>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Técnico</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programada</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completada</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="visit in visits" :key="visit.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ visit.ticket?.ticket_number || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="visit.type === 'preventive' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800'"
                                >
                                    {{ visit.type === 'preventive' ? 'Preventiva' : 'Correctiva' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ visit.technician?.name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="statusColors[visit.status] || 'bg-gray-100 text-gray-800'"
                                >
                                    {{ statusLabels[visit.status] || visit.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ visit.scheduled_at ? new Date(visit.scheduled_at).toLocaleString() : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ visit.completed_at ? new Date(visit.completed_at).toLocaleString() : 'N/A' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mt-4 text-sm text-gray-500">Total: {{ visits.length }} visitas</p>
        </div>
    </AuthenticatedLayout>
</template>
