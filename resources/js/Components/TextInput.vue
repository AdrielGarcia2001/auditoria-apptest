<script setup>
defineProps({
    modelValue: { type: [String, Number], default: '' },
    type: { type: String, default: 'text' },
    id: { type: String, default: undefined },
    name: { type: String, default: undefined },
    placeholder: { type: String, default: undefined },
    autocomplete: { type: String, default: undefined },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <input
        :id="id"
        :name="name"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :autocomplete="autocomplete"
        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
