<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const form = useForm({
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirmar contraseña" />

        <p class="mb-4 text-sm text-gray-600">
            Esta es una área segura. Confirma tu contraseña antes de continuar.
        </p>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="password" value="Contraseña" label="Contraseña" />

                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    name="password"
                    autocomplete="current-password"
                    class="mt-1 block w-full"
                />

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton :disabled="form.processing">
                    Confirmar
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
