<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    status: { type: String, default: '' },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Recuperar contraseña" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <InputLabel
                for="email"
                value="Correo electrónico"
                label="Correo electrónico"
            />

            <TextInput
                id="email"
                v-model="form.email"
                type="email"
                name="email"
                autocomplete="username"
                class="mt-1 block w-full"
            />

            <InputError :message="form.errors.email" class="mt-2" />

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton :disabled="form.processing">
                    Enviar enlace
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
