<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    visit: Object,
});

const form = useForm({
    file: null,
    description: '',
    evidencable_id: props.visit.id,
    evidencable_type: 'visit',
});

const fileInput = ref(null);

const submit = () => {
    form.post(route('evidence.store'), {
        onSuccess: () => {
            form.reset();
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        },
    });
};

const destroy = (evidenceId) => {
    if (confirm('¿Eliminar esta evidencia?')) {
        form.delete(route('evidence.destroy', evidenceId));
    }
};

const formatSize = (bytes) => {
    if (!bytes) return 'N/A';
    const kb = bytes / 1024;
    if (kb < 1024) {
        return `${kb.toFixed(1)} KB`;
    }
    return `${(kb / 1024).toFixed(1)} MB`;
};
</script>

<template>
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Evidencias ({{ visit.evidence?.length || 0 }})</h2>

        <!-- Formulario para subir -->
        <div class="mb-4 p-4 border border-gray-200 rounded-lg">
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Archivo</label>
                    <input
                        ref="fileInput"
                        @change="form.file = $event.target.files[0]"
                        type="file"
                        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    />
                    <p v-if="form.errors.file" class="text-red-500 text-sm mt-1">{{ form.errors.file }}</p>
                </div>
                <input
                    v-model="form.description"
                    type="text"
                    placeholder="Descripción (opcional)"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
                <div class="flex justify-end">
                    <button
                        @click="submit"
                        :disabled="form.processing || !form.file"
                        class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
                    >
                        Subir evidencia
                    </button>
                </div>
            </div>
        </div>

        <!-- Lista de evidencias -->
        <div v-if="visit.evidence?.length" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div
                v-for="evidence in visit.evidence"
                :key="evidence.id"
                class="border border-gray-200 rounded-lg p-3"
            >
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ evidence.file_name }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ evidence.file_type }} • {{ formatSize(evidence.file_size) }}
                        </p>
                        <p v-if="evidence.description" class="text-xs text-gray-400 mt-1">
                            {{ evidence.description }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-2 ml-2">
                        <a
                            :href="`/storage/${evidence.file_path}`"
                            target="_blank"
                            class="text-blue-600 hover:text-blue-800 text-xs"
                        >
                            Ver
                        </a>
                        <button
                            @click="destroy(evidence.id)"
                            class="text-red-600 hover:text-red-800 text-xs"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <p v-else class="text-gray-500 text-sm">No hay evidencias subidas.</p>
    </div>
</template>
