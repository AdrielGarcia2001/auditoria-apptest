<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import ChecklistManager from './Partials/ChecklistManager.vue';
import FindingManager from './Partials/FindingManager.vue';
import EvidenceManager from './Partials/EvidenceManager.vue';

const props = defineProps({
    visit: Object,
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
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Visita #{{ visit.id }}</h1>
                    <p class="text-gray-600">{{ visit.ticket?.ticket_number }} - {{ visit.ticket?.title }}</p>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('visits.edit', visit.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                    >
                        Editar
                    </Link>
                    <Link
                        :href="route('visits.index')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                    >
                        Volver
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2 space-y-6">
                    <!-- Info de la visita -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Información de la Visita</h2>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ visit.type === 'preventive' ? 'Preventiva' : 'Correctiva' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                <dd>
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="statusColors[visit.status]"
                                    >
                                        {{ statusLabels[visit.status] }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Técnico</dt>
                                <dd class="text-sm text-gray-900">{{ visit.technician?.name || 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Programada</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ visit.scheduled_at ? new Date(visit.scheduled_at).toLocaleString() : 'N/A' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Iniciada</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ visit.started_at ? new Date(visit.started_at).toLocaleString() : 'N/A' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Completada</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ visit.completed_at ? new Date(visit.completed_at).toLocaleString() : 'N/A' }}
                                </dd>
                            </div>
                        </dl>
                        <div v-if="visit.notes" class="mt-4">
                            <dt class="text-sm font-medium text-gray-500">Notas</dt>
                            <dd class="text-sm text-gray-900 mt-1">{{ visit.notes }}</dd>
                        </div>
                    </div>

                    <!-- Geolocalización -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Geolocalización</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Inicio</h3>
                                <p class="text-sm text-gray-900">
                                    Lat: {{ visit.start_latitude || 'N/A' }}<br>
                                    Lng: {{ visit.start_longitude || 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Fin</h3>
                                <p class="text-sm text-gray-900">
                                    Lat: {{ visit.end_latitude || 'N/A' }}<br>
                                    Lng: {{ visit.end_longitude || 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Checklists -->
                    <ChecklistManager :visit="visit" />

                    <!-- Hallazgos -->
                    <FindingManager :visit="visit" />

                    <!-- Evidencias -->
                    <EvidenceManager :visit="visit" />
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Ticket Asociado</h2>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Número</dt>
                                <dd class="text-sm text-gray-900">{{ visit.ticket?.ticket_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Título</dt>
                                <dd class="text-sm text-gray-900">{{ visit.ticket?.title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                <dd class="text-sm text-gray-900">{{ visit.ticket?.status }}</dd>
                            </div>
                        </dl>
                        <Link
                            :href="route('tickets.show', visit.ticket_id)"
                            class="mt-4 inline-block text-blue-600 hover:text-blue-800 text-sm"
                        >
                            Ver ticket completo →
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
