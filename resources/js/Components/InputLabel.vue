<script setup>
defineProps({
    value: { type: String, default: '' },
    label: { type: String, default: '' },
    for: { type: String, default: undefined },
});
</script>

<template>
    <label v-if="label" :for="for" class="block text-sm font-medium text-gray-700">
        {{ label }}
    </label>
</template>
