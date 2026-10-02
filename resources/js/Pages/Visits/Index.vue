<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    visits: Object,
    filters: Object,
    statuses: Array,
    technicians: Array,
});

const statusLabels = computed(() => {
    const labels = {};
    props.statuses.forEach(status => {
        labels[status.value] = status.label;
    });
    return labels;
});

const statusColors = {
    scheduled: 'bg-yellow-100 text-yellow-800',
    in_route: 'bg-blue-100 text-blue-800',
    on_site: 'bg-purple-100 text-purple-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-gray-100 text-gray-800',
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Visitas</h1>
                <Link
                    :href="route('visits.create')"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
                >
                    Nueva Visita
                </Link>
            </div>

            <!-- Filtros -->
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select
                            :value="filters.status"
                            @change="$inertia.get(route('visits.index'), { status: $event.target.value }, { preserveState: true })"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Todos</option>
                            <option v-for="status in statuses" :key="status.value" :value="status.value">
                                {{ status.label }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        <select
                            :value="filters.type"
                            @change="$inertia.get(route('visits.index'), { type: $event.target.value }, { preserveState: true })"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Todos</option>
                            <option value="preventive">Preventiva</option>
                            <option value="corrective">Correctiva</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Técnico</label>
                        <select
                            :value="filters.technician_id"
                            @change="$inertia.get(route('visits.index'), { technician_id: $event.target.value }, { preserveState: true })"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Todos</option>
                            <option v-for="tech in technicians" :key="tech.id" :value="tech.id">
                                {{ tech.name }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Técnico</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Programada</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="visit in visits.data" :key="visit.id" class="hover:bg-gray-50">
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
                                    :class="statusColors[visit.status]"
                                >
                                    {{ statusLabels[visit.status] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ visit.scheduled_at ? new Date(visit.scheduled_at).toLocaleString() : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <Link
                                    :href="route('visits.show', visit.id)"
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                >
                                    Ver
                                </Link>
                                <Link
                                    :href="route('visits.edit', visit.id)"
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    Editar
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4 flex justify-center">
                <div class="flex space-x-2">
                    <Link
                        v-for="link in visits.links"
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
