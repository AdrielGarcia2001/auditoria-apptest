<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    visit: Object,
});

const showForm = ref(false);
const editingId = ref(null);

const form = useForm({
    title: '',
    description: '',
    severity: 'medium',
    recommendation: '',
});

const editForm = useForm({
    title: '',
    description: '',
    severity: 'medium',
    status: 'open',
    recommendation: '',
});

const severityColors = {
    low: 'bg-gray-100 text-gray-800',
    medium: 'bg-orange-100 text-orange-800',
    high: 'bg-red-100 text-red-800',
};

const statusLabels = {
    open: 'Abierto',
    in_progress: 'En progreso',
    resolved: 'Resuelto',
    closed: 'Cerrado',
};

const submit = () => {
    form.post(route('visits.findings.store', props.visit.id), {
        onSuccess: () => {
            form.reset();
            showForm.value = false;
        },
    });
};

const edit = (finding) => {
    editingId.value = finding.id;
    editForm.title = finding.title;
    editForm.description = finding.description;
    editForm.severity = finding.severity;
    editForm.status = finding.status;
    editForm.recommendation = finding.recommendation || '';
};

const update = (findingId) => {
    editForm.put(route('visits.findings.update', [props.visit.id, findingId]), {
        onSuccess: () => {
            editingId.value = null;
        },
    });
};

const destroy = (findingId) => {
    if (confirm('¿Eliminar este hallazgo?')) {
        editForm.delete(route('visits.findings.destroy', [props.visit.id, findingId]));
    }
};
</script>

<template>
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Hallazgos ({{ visit.findings?.length || 0 }})</h2>
            <button
                v-if="!showForm"
                @click="showForm = true"
                class="text-sm bg-orange-600 text-white px-3 py-1 rounded hover:bg-orange-700"
            >
                + Registrar hallazgo
            </button>
        </div>

        <!-- Formulario para agregar -->
        <div v-if="showForm" class="mb-4 p-4 border border-gray-200 rounded-lg">
            <div class="space-y-3">
                <input
                    v-model="form.title"
                    type="text"
                    placeholder="Título del hallazgo"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500"
                />
                <textarea
                    v-model="form.description"
                    placeholder="Descripción"
                    rows="3"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500"
                ></textarea>
                <div class="grid grid-cols-2 gap-3">
                    <select
                        v-model="form.severity"
                        class="border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500"
                    >
                        <option value="low">Severidad baja</option>
                        <option value="medium">Severidad media</option>
                        <option value="high">Severidad alta</option>
                    </select>
                    <input
                        v-model="form.recommendation"
                        type="text"
                        placeholder="Recomendación (opcional)"
                        class="border-gray-300 rounded-lg shadow-sm focus:border-orange-500 focus:ring-orange-500"
                    />
                </div>
                <div class="flex justify-end space-x-2">
                    <button
                        @click="showForm = false; form.reset()"
                        class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="submit"
                        :disabled="form.processing"
                        class="px-3 py-1 text-sm bg-orange-600 text-white rounded hover:bg-orange-700 disabled:opacity-50"
                    >
                        Registrar
                    </button>
                </div>
            </div>
        </div>

        <!-- Lista de hallazgos -->
        <div v-if="visit.findings?.length" class="space-y-3">
            <div
                v-for="finding in visit.findings"
                :key="finding.id"
                class="border border-gray-200 rounded-lg p-4"
            >
                <div v-if="editingId === finding.id" class="space-y-2">
                    <input
                        v-model="editForm.title"
                        type="text"
                        class="w-full border-gray-300 rounded shadow-sm text-sm"
                    />
                    <textarea
                        v-model="editForm.description"
                        rows="2"
                        class="w-full border-gray-300 rounded shadow-sm text-sm"
                    ></textarea>
                    <div class="grid grid-cols-2 gap-2">
                        <select
                            v-model="editForm.severity"
                            class="border-gray-300 rounded shadow-sm text-sm"
                        >
                            <option value="low">Baja</option>
                            <option value="medium">Media</option>
                            <option value="high">Alta</option>
                        </select>
                        <select
                            v-model="editForm.status"
                            class="border-gray-300 rounded shadow-sm text-sm"
                        >
                            <option value="open">Abierto</option>
                            <option value="in_progress">En progreso</option>
                            <option value="resolved">Resuelto</option>
                            <option value="closed">Cerrado</option>
                        </select>
                    </div>
                    <textarea
                        v-model="editForm.recommendation"
                        placeholder="Recomendación"
                        rows="2"
                        class="w-full border-gray-300 rounded shadow-sm text-sm"
                    ></textarea>
                    <div class="flex space-x-2">
                        <button
                            @click="update(finding.id)"
                            class="px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700"
                        >
                            Guardar
                        </button>
                        <button
                            @click="editingId = null"
                            class="px-2 py-1 text-xs border border-gray-300 rounded hover:bg-gray-50"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
                <div v-else>
                    <div class="flex justify-between items-start">
                        <h3 class="font-medium text-gray-900">{{ finding.title }}</h3>
                        <div class="flex items-center space-x-2">
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full"
                                :class="severityColors[finding.severity]"
                            >
                                {{ finding.severity }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ statusLabels[finding.status] || finding.status }}
                            </span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-700 mt-2">{{ finding.description }}</p>
                    <p v-if="finding.recommendation" class="text-sm text-gray-500 mt-1">
                        <strong>Recomendación:</strong> {{ finding.recommendation }}
                    </p>
                    <div class="flex space-x-2 mt-2">
                        <button
                            @click="edit(finding)"
                            class="text-indigo-600 hover:text-indigo-800 text-sm"
                        >
                            Editar
                        </button>
                        <button
                            @click="destroy(finding.id)"
                            class="text-red-600 hover:text-red-800 text-sm"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <p v-else class="text-gray-500 text-sm">No hay hallazgos registrados.</p>
    </div>
</template>
