<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    ticket: Object,
    assets: Array,
    crews: Array,
    technicians: Array,
});

const form = useForm({
    title: props.ticket.title,
    description: props.ticket.description,
    priority: props.ticket.priority,
    status: props.ticket.status,
    asset_id: props.ticket.asset_id,
    assigned_to: props.ticket.assigned_to || '',
    crew_id: props.ticket.crew_id || '',
    due_date: props.ticket.due_date ? props.ticket.due_date.split('T')[0] : '',
});

const submit = () => {
    form.put(route('tickets.update', props.ticket.id));
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6 max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Editar Ticket</h1>

            <form @submit.prevent="submit" class="bg-white p-6 rounded-lg shadow space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input
                        v-model="form.title"
                        type="text"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    ></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
                        <select
                            v-model="form.priority"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="low">Baja</option>
                            <option value="medium">Media</option>
                            <option value="high">Alta</option>
                            <option value="critical">Crítica</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select
                            v-model="form.status"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="pending">Pendiente</option>
                            <option value="assigned">Asignado</option>
                            <option value="in_progress">En progreso</option>
                            <option value="completed">Completado</option>
                            <option value="verified">Verificado</option>
                            <option value="reopened">Reabierto</option>
                            <option value="cancelled">Cancelado</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Activo/Equipo</label>
                    <select
                        v-model="form.asset_id"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        <option value="">Seleccionar...</option>
                        <option v-for="asset in assets" :key="asset.id" :value="asset.id">
                            {{ asset.name }} ({{ asset.code }})
                        </option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asignar a (técnico)</label>
                        <select
                            v-model="form.assigned_to"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Sin asignar</option>
                            <option v-for="tech in technicians" :key="tech.id" :value="tech.id">
                                {{ tech.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cuadrilla</label>
                        <select
                            v-model="form.crew_id"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Sin asignar</option>
                            <option v-for="crew in crews" :key="crew.id" :value="crew.id">
                                {{ crew.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha límite</label>
                    <input
                        v-model="form.due_date"
                        type="date"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <Link
                        :href="route('tickets.show', ticket.id)"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                    >
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
