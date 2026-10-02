<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    tickets: Array,
    technicians: Array,
});

const form = useForm({
    ticket_id: '',
    type: 'corrective',
    scheduled_at: '',
    notes: '',
});

const submit = () => {
    form.post(route('visits.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6 max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Crear Visita</h1>

            <form @submit.prevent="submit" class="bg-white p-6 rounded-lg shadow space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ticket</label>
                    <select
                        v-model="form.ticket_id"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        <option value="">Seleccionar...</option>
                        <option v-for="ticket in tickets" :key="ticket.id" :value="ticket.id">
                            {{ ticket.ticket_number }} - {{ ticket.title }}
                        </option>
                    </select>
                    <p v-if="form.errors.ticket_id" class="text-red-500 text-sm mt-1">{{ form.errors.ticket_id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de visita</label>
                    <select
                        v-model="form.type"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="corrective">Correctiva</option>
                        <option value="preventive">Preventiva</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha programada</label>
                    <input
                        v-model="form.scheduled_at"
                        type="datetime-local"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    />
                    <p v-if="form.errors.scheduled_at" class="text-red-500 text-sm mt-1">{{ form.errors.scheduled_at }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                    <textarea
                        v-model="form.notes"
                        rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    ></textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <Link
                        :href="route('visits.index')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                    >
                        Crear Visita
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
