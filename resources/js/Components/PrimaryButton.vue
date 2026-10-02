<script setup>
defineProps({
    disabled: { type: Boolean, default: false },
    type: { type: String, default: 'submit' },
});

defineEmits(['click']);
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
        @click="$emit('click', $event)"
    >
        <slot />
    </button>
</template>
