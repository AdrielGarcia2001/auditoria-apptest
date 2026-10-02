<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    assets: Array,
    crews: Array,
    technicians: Array,
});

const form = useForm({
    title: '',
    description: '',
    priority: 'medium',
    asset_id: '',
    assigned_to: '',
    crew_id: '',
    due_date: '',
});

const submit = () => {
    form.post(route('tickets.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6 max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Crear Ticket</h1>

            <form @submit.prevent="submit" class="bg-white p-6 rounded-lg shadow space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                    <input
                        v-model="form.title"
                        type="text"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    />
                    <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    ></textarea>
                    <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</p>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha límite</label>
                        <input
                            v-model="form.due_date"
                            type="date"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
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
                    <p v-if="form.errors.asset_id" class="text-red-500 text-sm mt-1">{{ form.errors.asset_id }}</p>
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

                <div class="flex justify-end space-x-3 pt-4">
                    <Link
                        :href="route('tickets.index')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                    >
                        Crear Ticket
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
