<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    status: { type: String, default: '' },
    canResetPassword: { type: Boolean, default: true },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar sesión" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

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

            <div class="mt-4 block">
                <label class="flex items-center">
                    <input
                        v-model="form.remember"
                        type="checkbox"
                        name="remember"
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    >
                    <span class="ms-2 text-sm text-gray-600">Recordarme</span>
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <a
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900"
                >
                    ¿Olvidaste tu contraseña?
                </a>

                <PrimaryButton class="ms-4" :disabled="form.processing">
                    Iniciar sesión
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
