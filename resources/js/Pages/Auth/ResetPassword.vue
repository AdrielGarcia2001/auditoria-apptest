<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({
    email: { type: String, default: '' },
    token: { type: String, required: true },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Restablecer contraseña" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Correo electrónico" label="Correo electrónico" />

                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    name="email"
                    autocomplete="username"
                    class="mt-1 block w-full"
                />

                <InputError :message="form.errors.email" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Contraseña" label="Nueva contraseña" />

                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    class="mt-1 block w-full"
                />

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar contraseña"
                    label="Confirmar contraseña"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password"
                    class="mt-1 block w-full"
                />

                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton :disabled="form.processing">
                    Restablecer
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
