<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    visit: Object,
});

const showForm = ref(false);
const editingId = ref(null);

const form = useForm({
    item_name: '',
    description: '',
    order: 0,
});

const editForm = useForm({
    item_name: '',
    description: '',
    is_completed: false,
    notes: '',
});

const submit = () => {
    form.post(route('visits.checklists.store', props.visit.id), {
        onSuccess: () => {
            form.reset();
            showForm.value = false;
        },
    });
};

const edit = (item) => {
    editingId.value = item.id;
    editForm.item_name = item.item_name;
    editForm.description = item.description || '';
    editForm.is_completed = item.is_completed;
    editForm.notes = item.notes || '';
};

const update = (itemId) => {
    editForm.put(route('visits.checklists.update', [props.visit.id, itemId]), {
        onSuccess: () => {
            editingId.value = null;
        },
    });
};

const destroy = (itemId) => {
    if (confirm('¿Eliminar este ítem?')) {
        editForm.delete(route('visits.checklists.destroy', [props.visit.id, itemId]));
    }
};

const toggleComplete = (item) => {
    editForm.is_completed = !item.is_completed;
    editForm.put(route('visits.checklists.update', [props.visit.id, item.id]), {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Checklist ({{ visit.checklists?.length || 0 }})</h2>
            <button
                v-if="!showForm"
                @click="showForm = true"
                class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700"
            >
                + Agregar ítem
            </button>
        </div>

        <!-- Formulario para agregar -->
        <div v-if="showForm" class="mb-4 p-4 border border-gray-200 rounded-lg">
            <div class="space-y-3">
                <input
                    v-model="form.item_name"
                    type="text"
                    placeholder="Nombre del ítem"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
                <textarea
                    v-model="form.description"
                    placeholder="Descripción (opcional)"
                    rows="2"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                ></textarea>
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
                        class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
                    >
                        Agregar
                    </button>
                </div>
            </div>
        </div>

        <!-- Lista de ítems -->
        <div v-if="visit.checklists?.length" class="space-y-2">
            <div
                v-for="item in visit.checklists"
                :key="item.id"
                class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg"
            >
                <input
                    type="checkbox"
                    :checked="item.is_completed"
                    @change="toggleComplete(item)"
                    class="mt-1 h-4 w-4 text-blue-600 rounded"
                />
                <div class="flex-1">
                    <div v-if="editingId === item.id" class="space-y-2">
                        <input
                            v-model="editForm.item_name"
                            type="text"
                            class="w-full border-gray-300 rounded shadow-sm text-sm"
                        />
                        <textarea
                            v-model="editForm.notes"
                            placeholder="Notas"
                            rows="2"
                            class="w-full border-gray-300 rounded shadow-sm text-sm"
                        ></textarea>
                        <div class="flex space-x-2">
                            <button
                                @click="update(item.id)"
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
                        <p :class="item.is_completed ? 'line-through text-gray-500' : 'text-gray-900'" class="font-medium">
                            {{ item.item_name }}
                        </p>
                        <p v-if="item.description" class="text-sm text-gray-500">{{ item.description }}</p>
                        <p v-if="item.notes" class="text-sm text-gray-400 mt-1">Notas: {{ item.notes }}</p>
                    </div>
                </div>
                <div v-if="editingId !== item.id" class="flex space-x-2">
                    <button
                        @click="edit(item)"
                        class="text-indigo-600 hover:text-indigo-800 text-sm"
                    >
                        Editar
                    </button>
                    <button
                        @click="destroy(item.id)"
                        class="text-red-600 hover:text-red-800 text-sm"
                    >
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
        <p v-else class="text-gray-500 text-sm">No hay ítems en el checklist.</p>
    </div>
</template>
