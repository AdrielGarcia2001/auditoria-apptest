<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    visit: Object,
    tickets: Array,
    technicians: Array,
});

const form = useForm({
    status: props.visit.status,
    scheduled_at: props.visit.scheduled_at ? props.visit.scheduled_at.slice(0, 16) : '',
    started_at: props.visit.started_at ? props.visit.started_at.slice(0, 16) : '',
    completed_at: props.visit.completed_at ? props.visit.completed_at.slice(0, 16) : '',
    start_latitude: props.visit.start_latitude || '',
    start_longitude: props.visit.start_longitude || '',
    end_latitude: props.visit.end_latitude || '',
    end_longitude: props.visit.end_longitude || '',
    notes: props.visit.notes || '',
});

const gpsLoading = ref(false);
const gpsError = ref('');

const captureGPS = (type) => {
    if (!navigator.geolocation) {
        gpsError.value = 'Geolocalización no soportada por el navegador';
        return;
    }

    gpsLoading.value = true;
    gpsError.value = '';

    navigator.geolocation.getCurrentPosition(
        (position) => {
            if (type === 'start') {
                form.start_latitude = position.coords.latitude;
                form.start_longitude = position.coords.longitude;
            } else {
                form.end_latitude = position.coords.latitude;
                form.end_longitude = position.coords.longitude;
            }
            gpsLoading.value = false;
        },
        (error) => {
            gpsError.value = 'Error al obtener ubicación: ' + error.message;
            gpsLoading.value = false;
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
};

const submit = () => {
    form.put(route('visits.update', props.visit.id));
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6 max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Editar Visita</h1>

            <form @submit.prevent="submit" class="bg-white p-6 rounded-lg shadow space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select
                        v-model="form.status"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option value="scheduled">Programada</option>
                        <option value="in_route">En ruta</option>
                        <option value="on_site">En sitio</option>
                        <option value="completed">Completada</option>
                        <option value="cancelled">Cancelada</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha programada</label>
                    <input
                        v-model="form.scheduled_at"
                        type="datetime-local"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Iniciada</label>
                        <input
                            v-model="form.started_at"
                            type="datetime-local"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Completada</label>
                        <input
                            v-model="form.completed_at"
                            type="datetime-local"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                </div>

                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-sm font-medium text-gray-700">Geolocalización</h3>
                        <div class="flex space-x-2">
                            <button
                                type="button"
                                @click="captureGPS('start')"
                                :disabled="gpsLoading"
                                class="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50"
                            >
                                {{ gpsLoading ? 'Obteniendo...' : 'Capturar inicio' }}
                            </button>
                            <button
                                type="button"
                                @click="captureGPS('end')"
                                :disabled="gpsLoading"
                                class="px-3 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
                            >
                                {{ gpsLoading ? 'Obteniendo...' : 'Capturar fin' }}
                            </button>
                        </div>
                    </div>
                    <p v-if="gpsError" class="text-red-500 text-sm mb-2">{{ gpsError }}</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Latitud inicio</label>
                            <input
                                v-model="form.start_latitude"
                                type="number"
                                step="any"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Longitud inicio</label>
                            <input
                                v-model="form.start_longitude"
                                type="number"
                                step="any"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Latitud fin</label>
                            <input
                                v-model="form.end_latitude"
                                type="number"
                                step="any"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Longitud fin</label>
                            <input
                                v-model="form.end_longitude"
                                type="number"
                                step="any"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                    </div>
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
                        :href="route('visits.show', visit.id)"
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
